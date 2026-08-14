<?php

namespace App\Http\Controllers;

use App\Exports\IsoladosExport;
use App\Models\Isolado;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class RelatorioController extends Controller
{
    public function index(Request $request): View
    {
        $filtros = $this->filtros($request);
        $registros = $this->registrosFiltrados($filtros);

        return view('relatorios.index', [
            'filtros' => $filtros,
            'total' => $registros->count(),
            'porGenero' => $registros->groupBy('genero')->map->count()->sortDesc(),
            'porConservacao' => $registros->filter(fn ($i) => filled($i->conservacao))->groupBy('conservacao')->map->count()->sortDesc(),
            'porMeioCultivo' => $registros->filter(fn ($i) => filled($i->meio_cultivo))->groupBy('meio_cultivo')->map->count()->sortDesc(),
            'porAutor' => $registros->filter(fn ($i) => filled($i->autor))->groupBy('autor')->map->count()->sortDesc(),
            'generos' => Isolado::valoresDistintos('genero'),
            'especies' => Isolado::all()->map(fn ($i) => $i->especieCompleta())->unique()->sort()->values(),
            'autores' => Isolado::valoresDistintos('autor'),
            'locais' => Isolado::valoresDistintos('local'),
            'conservacoes' => Isolado::valoresDistintos('conservacao'),
            'meiosCultivo' => Isolado::valoresDistintos('meio_cultivo'),
        ]);
    }

    public function exportarExcel(Request $request): BinaryFileResponse
    {
        $registros = $this->registrosFiltrados($this->filtros($request));

        return Excel::download(new IsoladosExport($registros), 'relatorio-acervo-'.now()->format('Y-m-d-His').'.xlsx');
    }

    public function exportarPdf(Request $request): Response
    {
        $filtros = $this->filtros($request);
        $registros = $this->registrosFiltrados($filtros);

        $pdf = Pdf::loadView('relatorios.pdf', [
            'registros' => $registros,
            'filtros' => $filtros,
            'total' => $registros->count(),
        ])->setPaper('a4', 'landscape');

        return $pdf->download('relatorio-acervo-'.now()->format('Y-m-d-His').'.pdf');
    }

    protected function filtros(Request $request): array
    {
        return [
            'data_inicio' => $request->query('data_inicio', ''),
            'data_fim' => $request->query('data_fim', ''),
            'genero' => $request->query('genero', ''),
            'especie' => $request->query('especie', ''),
            'autor' => $request->query('autor', ''),
            'local' => $request->query('local', ''),
            'conservacao' => $request->query('conservacao', ''),
            'meio_cultivo' => $request->query('meio_cultivo', ''),
        ];
    }

    protected function registrosFiltrados(array $filtros): Collection
    {
        return Isolado::query()
            ->when($filtros['data_inicio'] !== '', fn ($q) => $q->whereDate('data', '>=', $filtros['data_inicio']))
            ->when($filtros['data_fim'] !== '', fn ($q) => $q->whereDate('data', '<=', $filtros['data_fim']))
            ->when($filtros['genero'] !== '', fn ($q) => $q->where('genero', $filtros['genero']))
            ->when($filtros['autor'] !== '', fn ($q) => $q->where('autor', $filtros['autor']))
            ->when($filtros['local'] !== '', fn ($q) => $q->where('local', $filtros['local']))
            ->when($filtros['conservacao'] !== '', fn ($q) => $q->where('conservacao', $filtros['conservacao']))
            ->when($filtros['meio_cultivo'] !== '', fn ($q) => $q->where('meio_cultivo', $filtros['meio_cultivo']))
            ->get()
            ->when($filtros['especie'] !== '', fn ($c) => $c->filter(fn ($i) => $i->especieCompleta() === $filtros['especie']));
    }
}
