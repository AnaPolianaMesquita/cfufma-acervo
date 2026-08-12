<?php

namespace App\Http\Controllers;

use App\Models\Isolado;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AcervoController extends Controller
{
    protected const COLUNAS_ORDENAVEIS = [
        'codigo', 'genero', 'especie', 'origem', 'meio_cultivo',
        'data', 'conservacao', 'local', 'armazenamento', 'autor',
    ];

    public function index(Request $request): View
    {
        $busca = trim((string) $request->query('busca', ''));
        $genero = $request->query('genero', '');
        $conservacao = $request->query('conservacao', '');
        $ordenar = in_array($request->query('ordenar'), self::COLUNAS_ORDENAVEIS, true) ? $request->query('ordenar') : 'codigo';
        $direcao = $request->query('direcao', 'asc') === 'desc' ? 'desc' : 'asc';
        $porPagina = (int) $request->query('por_pagina', 10);
        $porPagina = in_array($porPagina, [10, 25, 50, 100], true) ? $porPagina : 10;

        $paginador = Isolado::query()
            ->when($busca !== '', fn ($q) => $q->where(function ($q) use ($busca) {
                $q->where('codigo', 'like', "%{$busca}%")
                    ->orWhere('genero', 'like', "%{$busca}%")
                    ->orWhere('especie', 'like', "%{$busca}%")
                    ->orWhere('autor', 'like', "%{$busca}%");
            }))
            ->when($genero !== '', fn ($q) => $q->where('genero', $genero))
            ->when($conservacao !== '', fn ($q) => $q->where('conservacao', $conservacao))
            ->orderBy($ordenar, $direcao)
            ->paginate($porPagina)
            ->withQueryString();

        return view('acervo.index', [
            'itens' => collect($paginador->items()),
            'filtros' => compact('busca', 'genero', 'conservacao', 'ordenar', 'direcao', 'porPagina'),
            'generos' => Isolado::valoresDistintos('genero'),
            'conservacoes' => Isolado::valoresDistintos('conservacao'),
            'meta' => [
                'current_page' => $paginador->currentPage(),
                'last_page' => $paginador->lastPage(),
                'per_page' => $paginador->perPage(),
                'total' => $paginador->total(),
                'from' => $paginador->firstItem() ?? 0,
                'to' => $paginador->lastItem() ?? 0,
            ],
        ]);
    }

    public function create(): View
    {
        return view('acervo.create', [
            'isolado' => null,
            'generos' => Isolado::valoresDistintos('genero'),
            'meiosCultivo' => Isolado::valoresDistintos('meio_cultivo'),
            'conservacoes' => Isolado::valoresDistintos('conservacao'),
            'locais' => Isolado::valoresDistintos('local'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $dados = $request->validate([
            'codigo' => ['required', 'string', 'max:50', 'unique:isolados,codigo'],
            'genero' => ['required', 'string', 'max:100'],
            'especie' => ['required', 'string', 'max:100'],
            'origem' => ['nullable', 'string', 'max:150'],
            'meio_cultivo' => ['nullable', 'string', 'max:150'],
            'data' => ['nullable', 'date'],
            'conservacao' => ['nullable', 'string', 'max:150'],
            'local' => ['nullable', 'string', 'max:150'],
            'armazenamento' => ['nullable', 'string', 'max:150'],
            'autor' => ['required', 'string', 'max:150'],
        ]);

        Isolado::create($dados);

        return redirect()->route('acervo.index')->with('success', 'Isolado cadastrado com sucesso.');
    }

    public function show(int $id): View
    {
        return view('acervo.show', [
            'isolado' => Isolado::findOrFail($id),
        ]);
    }

    public function edit(int $id): View
    {
        return view('acervo.edit', [
            'isolado' => Isolado::findOrFail($id),
            'generos' => Isolado::valoresDistintos('genero'),
            'meiosCultivo' => Isolado::valoresDistintos('meio_cultivo'),
            'conservacoes' => Isolado::valoresDistintos('conservacao'),
            'locais' => Isolado::valoresDistintos('local'),
        ]);
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $isolado = Isolado::findOrFail($id);

        $dados = $request->validate([
            'codigo' => ['required', 'string', 'max:50', 'unique:isolados,codigo,'.$isolado->id],
            'genero' => ['required', 'string', 'max:100'],
            'especie' => ['required', 'string', 'max:100'],
            'origem' => ['nullable', 'string', 'max:150'],
            'meio_cultivo' => ['nullable', 'string', 'max:150'],
            'data' => ['nullable', 'date'],
            'conservacao' => ['nullable', 'string', 'max:150'],
            'local' => ['nullable', 'string', 'max:150'],
            'armazenamento' => ['nullable', 'string', 'max:150'],
            'autor' => ['required', 'string', 'max:150'],
        ]);

        $isolado->update($dados);

        return redirect()->route('acervo.show', $id)->with('success', 'Isolado atualizado com sucesso.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Isolado::findOrFail($id)->delete();

        return redirect()->route('acervo.index')->with('success', 'Isolado excluído com sucesso.');
    }

    public function destroyMultiple(Request $request): RedirectResponse
    {
        $ids = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ])['ids'];

        $total = Isolado::whereIn('id', $ids)->delete();

        return redirect()->route('acervo.index')->with('success', "{$total} isolado(s) excluído(s) com sucesso.");
    }
}
