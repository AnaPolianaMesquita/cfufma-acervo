<?php

namespace Tests\Feature;

use App\Models\Isolado;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RelatorioExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_pode_exportar_relatorio_em_excel(): void
    {
        $user = User::factory()->create();

        Isolado::create([
            'codigo' => 'ISO-200',
            'genero' => 'Fusarium',
            'especie' => 'oxysporum',
            'autor' => 'Teste',
        ]);

        $response = $this->actingAs($user)->get(route('relatorios.exportar.excel'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_pode_exportar_relatorio_em_pdf(): void
    {
        $user = User::factory()->create();

        Isolado::create([
            'codigo' => 'ISO-201',
            'genero' => 'Rhizopus',
            'especie' => 'stolonifer',
            'autor' => 'Teste',
        ]);

        $response = $this->actingAs($user)->get(route('relatorios.exportar.pdf'));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_exportacao_respeita_filtros_aplicados(): void
    {
        $user = User::factory()->create();

        Isolado::create(['codigo' => 'ISO-202', 'genero' => 'Fusarium', 'especie' => 'a', 'autor' => 'Teste']);
        Isolado::create(['codigo' => 'ISO-203', 'genero' => 'Rhizopus', 'especie' => 'b', 'autor' => 'Teste']);

        $response = $this->actingAs($user)->get(route('relatorios.exportar.pdf', ['genero' => 'Fusarium']));

        $response->assertOk();
    }

    public function test_visitante_sem_login_nao_exporta(): void
    {
        $response = $this->get(route('relatorios.exportar.excel'));

        $response->assertRedirect(route('login'));
    }
}
