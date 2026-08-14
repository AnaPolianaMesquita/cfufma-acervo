<?php

namespace Tests\Feature;

use App\Models\Isolado;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GaleriaPublicaTest extends TestCase
{
    use RefreshDatabase;

    public function test_visitante_sem_login_ve_a_galeria(): void
    {
        Storage::fake('public');

        Isolado::create([
            'codigo' => 'ISO-100',
            'genero' => 'Trichoderma',
            'especie' => 'harzianum',
            'autor' => 'Teste',
            'descricao' => 'Fungo usado no controle biológico de pragas.',
            'imagem' => UploadedFile::fake()->image('foto.jpg')->store('isolados', 'public'),
        ]);

        $response = $this->get(route('galeria.index'));

        $response->assertOk();
        $response->assertSee('Trichoderma');
        $response->assertSee('harzianum');
    }

    public function test_visitante_sem_login_ve_detalhe_da_especie(): void
    {
        $isolado = Isolado::create([
            'codigo' => 'ISO-101',
            'genero' => 'Aspergillus',
            'especie' => 'niger',
            'autor' => 'Teste',
            'descricao' => 'Fungo filamentoso comum em solo.',
        ]);

        $response = $this->get(route('galeria.show', $isolado->id));

        $response->assertOk();
        $response->assertSee('Aspergillus');
        $response->assertSee('Fungo filamentoso comum em solo.');
    }

    public function test_visitante_sem_login_nao_acessa_acervo_administrativo(): void
    {
        $response = $this->get(route('acervo.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_raiz_redireciona_visitante_para_galeria(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('galeria.index'));
    }
}
