<?php

namespace App\Http\Controllers;

use App\Models\Importacao;
use App\Models\Isolado;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $isolados = Isolado::all();

        $porGenero = $isolados
            ->groupBy('genero')
            ->map->count()
            ->sortDesc();

        $porConservacao = $isolados
            ->filter(fn ($i) => filled($i->conservacao))
            ->groupBy('conservacao')
            ->map->count()
            ->sortDesc();

        return view('dashboard.index', [
            'stats' => [
                'isolados' => $isolados->count(),
                'especies' => $isolados->map(fn ($i) => $i->especieCompleta())->unique()->count(),
                'generos' => $isolados->pluck('genero')->unique()->count(),
                'autores' => $isolados->pluck('autor')->filter()->unique()->count(),
                'importacoes' => Importacao::count(),
            ],
            'ultimaImportacao' => Importacao::latest()->first(),
            'ultimosRegistros' => $isolados->sortByDesc(fn ($i) => $i->data ?? $i->created_at)->take(5),
            'porGenero' => $porGenero,
            'porConservacao' => $porConservacao,
        ]);
    }
}
