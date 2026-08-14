<?php

namespace Tests\Feature;

use App\Models\Isolado;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FiltroFotoTest extends TestCase
{
    use RefreshDatabase;

    protected function criarIsolados(): void
    {
        Storage::fake('public');

        Isolado::create([
            'codigo' => 'ISO-300',
            'genero' => 'Mucor',
            'especie' => 'com-foto',
            'autor' => 'Teste',
            'imagem' => UploadedFile::fake()->image('foto.jpg')->store('isolados', 'public'),
        ]);

        Isolado::create([
            'codigo' => 'ISO-301',
            'genero' => 'Mucor',
            'especie' => 'sem-foto',
            'autor' => 'Teste',
        ]);
    }

    public function test_filtro_com_foto_no_acervo_interno(): void
    {
        $this->criarIsolados();
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('acervo.index', ['foto' => 'com']));

        $response->assertOk();
        $response->assertSee('ISO-300');
        $response->assertDontSee('ISO-301');
    }

    public function test_filtro_sem_foto_na_galeria_publica(): void
    {
        $this->criarIsolados();

        $response = $this->get(route('galeria.index', ['foto' => 'sem']));

        $response->assertOk();
        $response->assertSee('ISO-301');
        $response->assertDontSee('ISO-300');
    }
}
