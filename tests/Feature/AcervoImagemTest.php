<?php

namespace Tests\Feature;

use App\Models\Isolado;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AcervoImagemTest extends TestCase
{
    use RefreshDatabase;

    public function test_pode_cadastrar_isolado_com_imagem_e_descricao(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('acervo.store'), [
            'codigo' => 'ISO-999',
            'genero' => 'Aspergillus',
            'especie' => 'niger',
            'autor' => 'Teste',
            'descricao' => 'Fungo filamentoso comum em solo.',
            'imagem' => UploadedFile::fake()->image('foto.jpg'),
        ]);

        $response->assertRedirect(route('acervo.index'));

        $isolado = Isolado::where('codigo', 'ISO-999')->firstOrFail();

        $this->assertNotNull($isolado->imagem);
        $this->assertSame('Fungo filamentoso comum em solo.', $isolado->descricao);
        Storage::disk('public')->assertExists($isolado->imagem);
    }

    public function test_pode_remover_imagem_ao_editar(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();
        $isolado = Isolado::create([
            'codigo' => 'ISO-998',
            'genero' => 'Penicillium',
            'especie' => 'sp.',
            'autor' => 'Teste',
            'imagem' => UploadedFile::fake()->image('foto.jpg')->store('isolados', 'public'),
        ]);

        $caminhoAntigo = $isolado->imagem;

        $response = $this->actingAs($user)->put(route('acervo.update', $isolado->id), [
            'codigo' => $isolado->codigo,
            'genero' => $isolado->genero,
            'especie' => $isolado->especie,
            'autor' => $isolado->autor,
            'remover_imagem' => '1',
        ]);

        $response->assertRedirect(route('acervo.show', $isolado->id));

        $this->assertNull($isolado->fresh()->imagem);
        Storage::disk('public')->assertMissing($caminhoAntigo);
    }
}
