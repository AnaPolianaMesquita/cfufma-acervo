<?php

namespace App\Support;

use Illuminate\Support\Collection;

/**
 * Fonte de dados fictícia usada apenas nesta fase de interface.
 * Nenhum dado aqui é persistido — será substituída por Models/DB reais depois.
 */
class MockData
{
    protected static array $generos = [
        'Aspergillus', 'Penicillium', 'Candida', 'Fusarium', 'Trichoderma',
        'Rhizopus', 'Mucor', 'Cladosporium', 'Colletotrichum', 'Trichophyton',
    ];

    protected static array $especiesPorGenero = [
        'Aspergillus' => ['niger', 'flavus', 'fumigatus', 'terreus'],
        'Penicillium' => ['chrysogenum', 'citrinum', 'expansum'],
        'Candida' => ['albicans', 'tropicalis', 'krusei'],
        'Fusarium' => ['oxysporum', 'solani', 'graminearum'],
        'Trichoderma' => ['harzianum', 'viride', 'reesei'],
        'Rhizopus' => ['oryzae', 'stolonifer'],
        'Mucor' => ['circinelloides', 'racemosus'],
        'Cladosporium' => ['cladosporioides', 'herbarum'],
        'Colletotrichum' => ['gloeosporioides', 'acutatum'],
        'Trichophyton' => ['rubrum', 'mentagrophytes'],
    ];

    protected static array $origens = [
        'Solo', 'Água doce', 'Água do mar', 'Ar', 'Folha em decomposição',
        'Fruta', 'Casca de árvore', 'Sedimento marinho', 'Planta hospedeira', 'Pele humana',
    ];

    protected static array $meiosCultivo = [
        'Ágar Sabouraud', 'BDA - Ágar Batata Dextrose', 'Ágar Czapek',
        'Ágar Malte', 'YPD', 'Ágar Sangue',
    ];

    protected static array $conservacoes = [
        'Liofilização', 'Óleo mineral', 'Nitrogênio líquido',
        'Água destilada (Castellani)', 'Sílica gel', 'Papel de filtro',
    ];

    protected static array $locais = [
        'Freezer -80°C', 'Freezer -20°C', 'Geladeira 4°C',
        'Temperatura ambiente', 'Tanque criogênico',
    ];

    protected static array $armazenamentos = [
        'Criotubo', 'Tubo de ensaio', 'Ampola selada', 'Envelope de papel', 'Frasco âmbar',
    ];

    protected static array $autores = [
        'Maria Silva', 'João Souza', 'Ana Ferreira', 'Carlos Lima',
        'Beatriz Costa', 'Rafael Alves', 'Fernanda Nunes', 'Pedro Rocha',
    ];

    /** @return Collection<int, array<string, mixed>> */
    public static function isolados(): Collection
    {
        static $cache = null;

        if ($cache !== null) {
            return $cache;
        }

        $total = 64;
        $dataBase = strtotime('2026-08-11');
        $registros = [];

        for ($i = 1; $i <= $total; $i++) {
            $genero = self::$generos[($i - 1) % count(self::$generos)];
            $especiesDoGenero = self::$especiesPorGenero[$genero];
            $especie = $especiesDoGenero[($i - 1) % count($especiesDoGenero)];

            $registros[] = [
                'id' => $i,
                'codigo' => sprintf('ISO-%03d', $i),
                'genero' => $genero,
                'especie' => $especie,
                'especie_completa' => $genero.' '.$especie,
                'origem' => self::$origens[($i * 3) % count(self::$origens)],
                'meio_cultivo' => self::$meiosCultivo[($i * 2) % count(self::$meiosCultivo)],
                'data' => date('Y-m-d', $dataBase - ($i * 86400 * 3)),
                'conservacao' => self::$conservacoes[($i * 5) % count(self::$conservacoes)],
                'local' => self::$locais[($i * 7) % count(self::$locais)],
                'armazenamento' => self::$armazenamentos[($i * 4) % count(self::$armazenamentos)],
                'autor' => self::$autores[($i * 11) % count(self::$autores)],
            ];
        }

        return $cache = collect($registros);
    }

    public static function generos(): Collection
    {
        return collect(self::$generos)->sort()->values();
    }

    public static function especies(): Collection
    {
        return self::isolados()->pluck('especie_completa')->unique()->sort()->values();
    }

    public static function autores(): Collection
    {
        return collect(self::$autores)->sort()->values();
    }

    public static function locais(): Collection
    {
        return collect(self::$locais);
    }

    public static function conservacoes(): Collection
    {
        return collect(self::$conservacoes);
    }

    public static function meiosCultivo(): Collection
    {
        return collect(self::$meiosCultivo);
    }

    public static function isolado(int $id): ?array
    {
        return self::isolados()->firstWhere('id', $id);
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function importacoes(): Collection
    {
        return collect([
            [
                'id' => 5,
                'arquivo' => 'acervo_miconiba_agosto_2026.xlsx',
                'tamanho' => '284 KB',
                'usuario' => 'Ana Sousa',
                'data' => '2026-08-10 14:32',
                'status' => 'concluida',
                'total_linhas' => 58,
                'importados' => 54,
                'invalidos' => 2,
                'duplicados' => 2,
            ],
            [
                'id' => 4,
                'arquivo' => 'isolados_julho_2026.xlsx',
                'tamanho' => '196 KB',
                'usuario' => 'João Souza',
                'data' => '2026-07-22 09:10',
                'status' => 'concluida',
                'total_linhas' => 40,
                'importados' => 40,
                'invalidos' => 0,
                'duplicados' => 0,
            ],
            [
                'id' => 3,
                'arquivo' => 'acervo_revisado_v2.xls',
                'tamanho' => '312 KB',
                'usuario' => 'Maria Silva',
                'data' => '2026-06-30 16:47',
                'status' => 'erro',
                'total_linhas' => 0,
                'importados' => 0,
                'invalidos' => 0,
                'duplicados' => 0,
            ],
            [
                'id' => 2,
                'arquivo' => 'lote_isolados_maio.xlsx',
                'tamanho' => '150 KB',
                'usuario' => 'Ana Sousa',
                'data' => '2026-05-14 11:05',
                'status' => 'concluida',
                'total_linhas' => 22,
                'importados' => 21,
                'invalidos' => 1,
                'duplicados' => 0,
            ],
            [
                'id' => 1,
                'arquivo' => 'acervo_inicial.xlsx',
                'tamanho' => '410 KB',
                'usuario' => 'Ana Sousa',
                'data' => '2026-04-02 08:20',
                'status' => 'concluida',
                'total_linhas' => 120,
                'importados' => 115,
                'invalidos' => 3,
                'duplicados' => 2,
            ],
        ]);
    }

    public static function importacao(int $id): ?array
    {
        return self::importacoes()->firstWhere('id', $id);
    }

    /** @return Collection<int, array<string, mixed>> */
    public static function usuarios(): Collection
    {
        return collect([
            ['id' => 1, 'nome' => 'Ana Sousa', 'email' => 'ana.sousa@ufma.br', 'perfil' => 'Administrador', 'status' => 'ativo', 'ultimo_acesso' => '2026-08-11 09:12'],
            ['id' => 2, 'nome' => 'João Souza', 'email' => 'joao.souza@ufma.br', 'perfil' => 'Curador', 'status' => 'ativo', 'ultimo_acesso' => '2026-08-10 17:44'],
            ['id' => 3, 'nome' => 'Maria Silva', 'email' => 'maria.silva@ufma.br', 'perfil' => 'Curador', 'status' => 'ativo', 'ultimo_acesso' => '2026-08-09 13:02'],
            ['id' => 4, 'nome' => 'Carlos Lima', 'email' => 'carlos.lima@ufma.br', 'perfil' => 'Consulta', 'status' => 'inativo', 'ultimo_acesso' => '2026-06-18 10:30'],
            ['id' => 5, 'nome' => 'Beatriz Costa', 'email' => 'beatriz.costa@ufma.br', 'perfil' => 'Consulta', 'status' => 'ativo', 'ultimo_acesso' => '2026-08-08 08:55'],
        ]);
    }

    public static function usuarioAtual(): array
    {
        return self::usuarios()->firstWhere('id', 1);
    }
}
