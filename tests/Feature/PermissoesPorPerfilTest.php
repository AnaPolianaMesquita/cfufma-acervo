<?php

namespace Tests\Feature;

use App\Models\PerfilPermissao;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissoesPorPerfilTest extends TestCase
{
    use RefreshDatabase;

    public function test_consulta_nao_acessa_importacao_por_padrao(): void
    {
        $user = User::factory()->create(['perfil' => 'Consulta']);

        $response = $this->actingAs($user)->get(route('importacao.index'));

        $response->assertForbidden();
    }

    public function test_administrador_pode_liberar_importacao_para_consulta(): void
    {
        $admin = User::factory()->create(['perfil' => 'Administrador']);
        $consulta = User::factory()->create(['perfil' => 'Consulta']);

        $this->actingAs($admin)->put(route('configuracoes.permissoes.update'), [
            'permissoes' => [
                'Curador' => ['acervo' => 1, 'importar' => 1, 'relatorios' => 1],
                'Consulta' => ['acervo' => 1, 'importar' => 1, 'relatorios' => 1],
            ],
        ])->assertRedirect(route('configuracoes.usuarios'));

        $response = $this->actingAs($consulta)->get(route('importacao.index'));

        $response->assertOk();
    }

    public function test_administrador_pode_restringir_relatorios_do_curador(): void
    {
        $admin = User::factory()->create(['perfil' => 'Administrador']);
        $curador = User::factory()->create(['perfil' => 'Curador']);

        $this->actingAs($curador)->get(route('relatorios.index'))->assertOk();

        $this->actingAs($admin)->put(route('configuracoes.permissoes.update'), [
            'permissoes' => [
                'Curador' => ['acervo' => 1, 'importar' => 1, 'relatorios' => 0],
                'Consulta' => ['acervo' => 1, 'importar' => 0, 'relatorios' => 1],
            ],
        ]);

        $this->actingAs($curador)->get(route('relatorios.index'))->assertForbidden();
    }

    public function test_permissoes_do_administrador_nao_podem_ser_alteradas_via_formulario(): void
    {
        $admin = User::factory()->create(['perfil' => 'Administrador']);

        $this->actingAs($admin)->put(route('configuracoes.permissoes.update'), [
            'permissoes' => [
                'Administrador' => ['acervo' => 0, 'importar' => 0, 'relatorios' => 0],
                'Curador' => ['acervo' => 1, 'importar' => 1, 'relatorios' => 1],
                'Consulta' => ['acervo' => 1, 'importar' => 0, 'relatorios' => 1],
            ],
        ]);

        $permissaoAdmin = PerfilPermissao::where('perfil', 'Administrador')->first();

        $this->assertTrue($permissaoAdmin->acervo);
        $this->assertTrue($permissaoAdmin->importar);
        $this->assertTrue($permissaoAdmin->relatorios);
    }

    public function test_apenas_administrador_pode_alterar_permissoes(): void
    {
        $curador = User::factory()->create(['perfil' => 'Curador']);

        $response = $this->actingAs($curador)->put(route('configuracoes.permissoes.update'), [
            'permissoes' => [
                'Curador' => ['acervo' => 1, 'importar' => 1, 'relatorios' => 1],
                'Consulta' => ['acervo' => 1, 'importar' => 1, 'relatorios' => 1],
            ],
        ]);

        $response->assertForbidden();
    }
}
