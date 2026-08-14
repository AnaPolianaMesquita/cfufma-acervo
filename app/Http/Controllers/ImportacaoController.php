<?php

namespace App\Http\Controllers;

use App\Models\Importacao;
use App\Services\IsoladoImportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ImportacaoController extends Controller
{
    public function index(): View
    {
        return view('importacao.index', [
            'colunasEsperadas' => ['Código', 'Gênero', 'Espécie', 'Origem', 'Meio de cultivo', 'Data', 'Conservação', 'Local', 'Armazenamento', 'Autor', 'Descrição', 'Foto (link)'],
        ]);
    }

    public function preview(Request $request, IsoladoImportService $service): JsonResponse
    {
        $request->validate([
            'arquivo' => ['required', 'file', 'mimes:xls,xlsx', 'max:10240'],
        ]);

        $analise = $service->analisar($request->file('arquivo'));

        if (isset($analise['erro'])) {
            return response()->json(['erro' => $analise['erro']], 422);
        }

        return response()->json([
            'headersFound' => $analise['headersFound'],
            'missingHeaders' => $analise['missingHeaders'],
            'totalLinhas' => $analise['totalLinhas'],
            'validCount' => $analise['validCount'],
            'invalidCount' => $analise['invalidCount'],
            'duplicateCount' => $analise['duplicateCount'],
            'amostra' => collect($analise['linhas'])->take(10)->values(),
        ]);
    }

    public function store(Request $request, IsoladoImportService $service): RedirectResponse|JsonResponse
    {
        $request->validate([
            'arquivo' => ['required', 'file', 'mimes:xls,xlsx', 'max:10240'],
        ]);

        $importacao = $service->importar($request->file('arquivo'), auth()->id());

        $mensagem = $importacao->status === 'concluida'
            ? 'Importação concluída com sucesso.'
            : 'Falha ao processar o arquivo.';

        session()->flash($importacao->status === 'concluida' ? 'success' : 'error', $mensagem);

        if ($request->ajax()) {
            return response()->json([
                'redirect' => route('importacao.show', $importacao->id),
            ]);
        }

        return redirect()->route('importacao.show', $importacao->id);
    }

    public function historico(): View
    {
        return view('importacao.historico', [
            'importacoes' => Importacao::orderByDesc('id')->get(),
        ]);
    }

    public function show(int $id): View
    {
        return view('importacao.show', [
            'importacao' => Importacao::findOrFail($id),
        ]);
    }
}
