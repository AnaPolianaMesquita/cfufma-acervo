<?php

namespace App\Services;

use App\Models\Importacao;
use App\Models\Isolado;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Throwable;

class IsoladoImportService
{
    /** Campo interno => variações de cabeçalho aceitas (já normalizadas: sem acento, minúsculas). */
    protected const CAMPOS = [
        'codigo' => ['codigo', 'cod'],
        'genero' => ['genero'],
        'especie' => ['especie'],
        'origem' => ['origem'],
        'meio_cultivo' => ['meio de cultivo', 'meio cultivo', 'meio'],
        'data' => ['data', 'data de coleta', 'data coleta'],
        'conservacao' => ['conservacao', 'metodo de conservacao', 'conservacao (metodo)'],
        'local' => ['local', 'local de armazenamento'],
        'armazenamento' => ['armazenamento', 'tipo de armazenamento'],
        'autor' => ['autor', 'responsavel'],
    ];

    protected const OBRIGATORIOS = ['codigo', 'genero', 'especie'];

    /**
     * Lê e analisa a planilha sem persistir nada.
     */
    public function analisar(UploadedFile $file): array
    {
        try {
            $sheets = Excel::toCollection(null, $file);
        } catch (Throwable) {
            return ['erro' => 'Não foi possível ler o arquivo. Verifique se é um .xls ou .xlsx válido.'];
        }

        $rows = $sheets->first();

        if (! $rows || $rows->isEmpty()) {
            return ['erro' => 'A planilha está vazia.'];
        }

        $headerRow = $rows->first();
        $dataRows = $rows->slice(1)
            ->filter(fn ($row) => $row->filter(fn ($v) => $v !== null && $v !== '')->isNotEmpty())
            ->values();

        $colunaPorCampo = [];
        foreach (self::CAMPOS as $campo => $candidatos) {
            foreach ($headerRow as $idx => $valor) {
                if (in_array($this->normalizar((string) $valor), $candidatos, true)) {
                    $colunaPorCampo[$campo] = $idx;
                    break;
                }
            }
        }

        $missingHeaders = collect(self::CAMPOS)->keys()
            ->reject(fn ($campo) => isset($colunaPorCampo[$campo]))
            ->map(fn ($campo) => $this->rotulo($campo))
            ->values()
            ->all();

        $codigosNoArquivo = [];

        $linhas = $dataRows->map(function ($row, $i) use ($colunaPorCampo, &$codigosNoArquivo) {
            $dados = [];
            foreach ($colunaPorCampo as $campo => $idx) {
                $valor = $row->get($idx);
                $dados[$campo] = is_string($valor) ? trim($valor) : $valor;
            }

            $faltantes = collect(self::OBRIGATORIOS)->filter(fn ($campo) => empty($dados[$campo] ?? null));

            $status = 'ok';
            $motivo = null;

            if ($faltantes->isNotEmpty()) {
                $status = 'invalido';
                $motivo = $faltantes->map(fn ($c) => $this->rotulo($c))->implode(', ');
            } elseif (isset($codigosNoArquivo[$dados['codigo']]) || Isolado::where('codigo', $dados['codigo'])->exists()) {
                $status = 'duplicado';
            }

            if ($status === 'ok') {
                $codigosNoArquivo[$dados['codigo']] = true;
            }

            return [
                'linha' => $i + 2,
                'dados' => $dados,
                'status' => $status,
                'motivo' => $motivo,
            ];
        })->values();

        return [
            'headersFound' => array_values(array_map(fn ($c) => $this->rotulo($c), array_keys($colunaPorCampo))),
            'missingHeaders' => $missingHeaders,
            'totalLinhas' => $linhas->count(),
            'linhas' => $linhas->all(),
            'validCount' => $linhas->where('status', 'ok')->count(),
            'invalidCount' => $linhas->where('status', 'invalido')->count(),
            'duplicateCount' => $linhas->where('status', 'duplicado')->count(),
        ];
    }

    /**
     * Analisa e persiste: cria os isolados válidos e registra o histórico da importação.
     */
    public function importar(UploadedFile $file, ?int $usuarioId): Importacao
    {
        $analise = $this->analisar($file);

        if (isset($analise['erro'])) {
            return Importacao::create([
                'arquivo' => $file->getClientOriginalName(),
                'tamanho' => $file->getSize(),
                'usuario_id' => $usuarioId,
                'status' => 'erro',
                'erro_mensagem' => $analise['erro'],
            ]);
        }

        $importacao = Importacao::create([
            'arquivo' => $file->getClientOriginalName(),
            'tamanho' => $file->getSize(),
            'usuario_id' => $usuarioId,
            'status' => 'concluida',
            'total_linhas' => $analise['totalLinhas'],
            'invalidos' => $analise['invalidCount'],
            'duplicados' => $analise['duplicateCount'],
            'colunas_ausentes' => $analise['missingHeaders'],
            'linhas_invalidas' => collect($analise['linhas'])
                ->where('status', 'invalido')
                ->map(fn ($l) => ['linha' => $l['linha'], 'motivo' => $l['motivo']])
                ->values()
                ->all(),
        ]);

        $importados = 0;

        foreach ($analise['linhas'] as $linha) {
            if ($linha['status'] !== 'ok') {
                continue;
            }

            $dados = $linha['dados'];

            try {
                Isolado::create([
                    'codigo' => $dados['codigo'],
                    'genero' => $dados['genero'],
                    'especie' => $dados['especie'],
                    'origem' => $dados['origem'] ?? null,
                    'meio_cultivo' => $dados['meio_cultivo'] ?? null,
                    'data' => $this->parseData($dados['data'] ?? null),
                    'conservacao' => $dados['conservacao'] ?? null,
                    'local' => $dados['local'] ?? null,
                    'armazenamento' => $dados['armazenamento'] ?? null,
                    'autor' => $dados['autor'] ?? null,
                    'importacao_id' => $importacao->id,
                ]);

                $importados++;
            } catch (Throwable) {
                // código duplicado detectado apenas no momento da gravação (corrida rara) — ignora a linha
            }
        }

        $importacao->update(['importados' => $importados]);

        return $importacao;
    }

    protected function parseData(mixed $valor): ?string
    {
        if (empty($valor)) {
            return null;
        }

        if (is_numeric($valor)) {
            try {
                return ExcelDate::excelToDateTimeObject($valor)->format('Y-m-d');
            } catch (Throwable) {
                return null;
            }
        }

        foreach (['d/m/Y', 'Y-m-d', 'd-m-Y'] as $formato) {
            try {
                return Carbon::createFromFormat($formato, trim((string) $valor))->format('Y-m-d');
            } catch (Throwable) {
                continue;
            }
        }

        try {
            return Carbon::parse((string) $valor)->format('Y-m-d');
        } catch (Throwable) {
            return null;
        }
    }

    protected function normalizar(string $valor): string
    {
        return Str::of($valor)->ascii()->lower()->squish()->toString();
    }

    protected function rotulo(string $campo): string
    {
        return match ($campo) {
            'codigo' => 'Código',
            'genero' => 'Gênero',
            'especie' => 'Espécie',
            'origem' => 'Origem',
            'meio_cultivo' => 'Meio de cultivo',
            'data' => 'Data',
            'conservacao' => 'Conservação',
            'local' => 'Local',
            'armazenamento' => 'Armazenamento',
            'autor' => 'Autor',
            default => $campo,
        };
    }
}
