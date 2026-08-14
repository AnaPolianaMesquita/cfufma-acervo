<?php

namespace Tests\Feature;

use App\Models\Isolado;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class ImportacaoFotoDescricaoTest extends TestCase
{
    use RefreshDatabase;

    protected function criarPlanilha(array $linhas): UploadedFile
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->fromArray(['Codigo', 'Genero', 'Especie', 'Autor', 'Descricao', 'Foto'], null, 'A1');

        foreach ($linhas as $i => $linha) {
            $sheet->fromArray($linha, null, 'A'.($i + 2));
        }

        $caminho = tempnam(sys_get_temp_dir(), 'planilha').'.xlsx';
        (new Xlsx($spreadsheet))->save($caminho);

        return new UploadedFile($caminho, 'planilha.xlsx', null, null, true);
    }

    public function test_importacao_grava_descricao_e_baixa_a_foto(): void
    {
        Storage::fake('public');

        Http::fake([
            'exemplo.com/*' => Http::response(
                base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII='),
                200,
                ['Content-Type' => 'image/png']
            ),
        ]);

        $user = User::factory()->create(['perfil' => 'Administrador']);

        $arquivo = $this->criarPlanilha([
            ['ISO-500', 'Aspergillus', 'niger', 'Teste', 'Fungo comum em solo.', 'https://exemplo.com/foto.png'],
        ]);

        $response = $this->actingAs($user)->post(route('importacao.store'), ['arquivo' => $arquivo]);

        $response->assertRedirect();

        $isolado = Isolado::where('codigo', 'ISO-500')->firstOrFail();

        $this->assertSame('Fungo comum em solo.', $isolado->descricao);
        $this->assertNotNull($isolado->imagem);
        Storage::disk('public')->assertExists($isolado->imagem);
    }

    public function test_link_de_foto_invalido_nao_impede_a_importacao(): void
    {
        Storage::fake('public');

        Http::fake([
            'exemplo.com/*' => Http::response('não encontrado', 404),
        ]);

        $user = User::factory()->create(['perfil' => 'Administrador']);

        $arquivo = $this->criarPlanilha([
            ['ISO-501', 'Mucor', 'sp.', 'Teste', '', 'https://exemplo.com/quebrado.png'],
        ]);

        $response = $this->actingAs($user)->post(route('importacao.store'), ['arquivo' => $arquivo]);

        $response->assertRedirect();

        $isolado = Isolado::where('codigo', 'ISO-501')->firstOrFail();

        $this->assertNull($isolado->imagem);
    }
}
