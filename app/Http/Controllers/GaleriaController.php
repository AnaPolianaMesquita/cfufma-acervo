<?php

namespace App\Http\Controllers;

use App\Models\Isolado;
use Illuminate\View\View;
use Illuminate\Http\Request;

class GaleriaController extends Controller
{
    public function index(Request $request): View
    {
        $busca = trim((string) $request->query('busca', ''));
        $genero = $request->query('genero', '');
        $foto = in_array($request->query('foto'), ['com', 'sem'], true) ? $request->query('foto') : '';

        $paginador = Isolado::query()
            ->when($busca !== '', fn ($q) => $q->where(function ($q) use ($busca) {
                $q->where('genero', 'like', "%{$busca}%")
                    ->orWhere('especie', 'like', "%{$busca}%")
                    ->orWhere('codigo', 'like', "%{$busca}%");
            }))
            ->when($genero !== '', fn ($q) => $q->where('genero', $genero))
            ->when($foto === 'com', fn ($q) => $q->whereNotNull('imagem')->where('imagem', '!=', ''))
            ->when($foto === 'sem', fn ($q) => $q->where(fn ($q) => $q->whereNull('imagem')->orWhere('imagem', '')))
            ->orderBy('genero')
            ->orderBy('especie')
            ->paginate(12)
            ->withQueryString();

        $todos = Isolado::all();

        return view('galeria.index', [
            'itens' => collect($paginador->items()),
            'filtros' => compact('busca', 'genero', 'foto'),
            'generos' => Isolado::valoresDistintos('genero'),
            'stats' => [
                'isolados' => $todos->count(),
                'especies' => $todos->map(fn ($i) => $i->especieCompleta())->unique()->count(),
                'generos' => $todos->pluck('genero')->unique()->count(),
                'comFoto' => $todos->whereNotNull('imagem')->where('imagem', '!=', '')->count(),
            ],
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

    public function show(int $id): View
    {
        return view('galeria.show', [
            'isolado' => Isolado::findOrFail($id),
        ]);
    }
}
