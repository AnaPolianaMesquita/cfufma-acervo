@extends('layouts.app')

@section('title', 'Importar Excel - Micoteca')
@section('page-title', 'Importação de Excel')

@section('content')

<div
    x-data="importWizard()"
    class="max-w-5xl mx-auto space-y-6"
>
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-ink">Importar planilha</h1>
            <p class="text-muted mt-1">Envie um arquivo XLS ou XLSX seguindo o modelo do acervo.</p>
        </div>
        <x-button variant="secondary" :href="route('importacao.historico')">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v5h5"/><path d="M3.05 13A9 9 0 1 0 6 5.3L3 8"/><path d="M12 7v5l4 2"/></svg>
            Histórico
        </x-button>
    </div>

    <!-- Stepper -->
    <div class="flex items-center gap-2 sm:gap-4">
        <template x-for="(label, idx) in ['Enviar arquivo', 'Pré-visualização', 'Resultado']" :key="idx">
            <div class="flex items-center gap-2 sm:gap-4 flex-1">
                <div class="flex items-center gap-2 shrink-0">
                    <div
                        class="w-7 h-7 rounded-full flex items-center justify-center text-xs font-semibold border"
                        :class="step > idx + 1 ? 'bg-brand border-brand text-white' : (step === idx + 1 ? 'border-brand text-brand bg-brand-light' : 'border-slate-200 text-slate-400')"
                    >
                        <svg x-show="step > idx + 1" xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg>
                        <span x-show="step <= idx + 1" x-text="idx + 1"></span>
                    </div>
                    <span class="text-sm font-medium hidden sm:inline" :class="step === idx + 1 ? 'text-ink' : 'text-muted'" x-text="label"></span>
                </div>
                <div class="h-px flex-1 bg-slate-200" x-show="idx < 2"></div>
            </div>
        </template>
    </div>

    <!-- Passo 1: Upload -->
    <div x-show="step === 1" x-cloak>
        <x-card>
            <div
                x-on:dragover.prevent="dragging = true"
                x-on:dragleave.prevent="dragging = false"
                x-on:drop.prevent="onDrop($event)"
                x-on:click="$refs.fileInput.click()"
                class="border-2 border-dashed rounded-2xl py-16 px-6 flex flex-col items-center justify-center text-center cursor-pointer transition-colors"
                :class="dragging ? 'border-brand bg-brand-light/50' : 'border-slate-200 hover:border-brand/60 hover:bg-slate-50'"
            >
                <input x-ref="fileInput" type="file" accept=".xls,.xlsx" class="hidden" x-on:change="onFileChange($event)">

                <div class="w-14 h-14 rounded-full bg-brand-light flex items-center justify-center text-brand-dark mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3v12m0 0-4-4m4 4 4-4M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2v-2"/></svg>
                </div>

                <p class="text-sm font-medium text-ink">Arraste o arquivo aqui ou clique para selecionar</p>
                <p class="text-xs text-muted mt-1">Formatos aceitos: .xls, .xlsx — tamanho máximo 10 MB</p>
            </div>

            <div x-show="file" x-cloak class="mt-5 flex items-center justify-between rounded-xl border border-slate-200 px-4 py-3">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-lg bg-brand-light flex items-center justify-center text-brand-dark shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M8 13h8"/><path d="M8 17h8"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-medium text-ink truncate" x-text="file?.name"></p>
                        <p class="text-xs text-muted" x-text="formatSize(file?.size)"></p>
                    </div>
                </div>
                <button type="button" x-on:click="file = null" class="text-slate-400 hover:text-red-500 shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="mt-6 flex justify-end">
                <x-button x-on:click="analyze()" :disabled="false" x-bind:disabled="!file || analyzing">
                    <svg x-show="analyzing" class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                    <span x-text="analyzing ? 'Analisando arquivo...' : 'Analisar arquivo'"></span>
                </x-button>
            </div>
        </x-card>

        <x-card class="mt-6">
            <h3 class="font-semibold text-ink mb-3">Colunas esperadas na planilha</h3>
            <div class="flex flex-wrap gap-2">
                @foreach ($colunasEsperadas as $coluna)
                    <x-badge variant="neutral">{{ $coluna }}</x-badge>
                @endforeach
            </div>
        </x-card>
    </div>

    <!-- Passo 2: Pré-visualização e validação -->
    <div x-show="step === 2" x-cloak class="space-y-6">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <x-card>
                <p class="text-xs text-muted">Registros encontrados</p>
                <p class="text-2xl font-bold text-ink mt-2" x-text="totalLinhas"></p>
            </x-card>
            <x-card>
                <p class="text-xs text-muted">Colunas encontradas</p>
                <p class="text-2xl font-bold text-ink mt-2" x-text="headersFound.length"></p>
            </x-card>
            <x-card>
                <p class="text-xs text-muted">Linhas inválidas</p>
                <p class="text-2xl font-bold text-red-600 mt-2" x-text="invalidCount"></p>
            </x-card>
            <x-card>
                <p class="text-xs text-muted">Duplicidades</p>
                <p class="text-2xl font-bold text-amber-600 mt-2" x-text="duplicateCount"></p>
            </x-card>
        </div>

        <x-card x-show="missingHeaders.length > 0">
            <x-alert variant="warning" title="Colunas ausentes na planilha" :dismissible="false">
                <span x-text="missingHeaders.join(', ')"></span> não foram encontradas e ficarão em branco para os registros importados.
            </x-alert>
        </x-card>

        <x-card :padded="false">
            <div class="p-6 border-b border-slate-100">
                <h3 class="font-semibold text-ink">Pré-visualização dos dados</h3>
                <p class="text-xs text-muted mt-1">Exibindo as primeiras linhas identificadas no arquivo (a importação completa considera todas as linhas).</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-muted border-b border-slate-100">
                            <th class="p-4 font-medium">Linha</th>
                            <th class="p-4 font-medium">Código</th>
                            <th class="p-4 font-medium">Gênero</th>
                            <th class="p-4 font-medium">Espécie</th>
                            <th class="p-4 font-medium">Origem</th>
                            <th class="p-4 font-medium">Autor</th>
                            <th class="p-4 font-medium">Situação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="row in previewRows" :key="row.linha">
                            <tr class="border-t border-slate-50" :class="row.status !== 'ok' ? 'bg-red-50/40' : ''">
                                <td class="p-4 text-slate-400" x-text="row.linha"></td>
                                <td class="p-4 font-medium text-ink" x-text="row.dados.codigo || '—'"></td>
                                <td class="p-4 text-slate-600" x-text="row.dados.genero || '—'"></td>
                                <td class="p-4 italic text-slate-600" x-text="row.dados.especie || '—'"></td>
                                <td class="p-4 text-slate-600" x-text="row.dados.origem || '—'"></td>
                                <td class="p-4 text-slate-600" x-text="row.dados.autor || '—'"></td>
                                <td class="p-4">
                                    <span x-show="row.status === 'ok'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-brand-light text-brand-dark border border-brand-light">Válido</span>
                                    <span x-show="row.status === 'invalido'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-100" x-text="'Campo ausente: ' + row.motivo"></span>
                                    <span x-show="row.status === 'duplicado'" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-100">Duplicado</span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </x-card>

        <div class="flex items-center justify-between">
            <x-button variant="secondary" x-on:click="step = 1">Voltar</x-button>
            <x-button x-on:click="confirmImport()" x-bind:disabled="processing">
                <svg x-show="processing" class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.219-8.56"/></svg>
                <span x-text="processing ? 'Importando...' : 'Confirmar importação'"></span>
            </x-button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    function importWizard() {
        return {
            step: 1,
            dragging: false,
            file: null,
            analyzing: false,
            processing: false,
            headersFound: [],
            missingHeaders: [],
            totalLinhas: 0,
            invalidCount: 0,
            duplicateCount: 0,
            previewRows: [],

            onDrop(e) {
                this.dragging = false;
                const f = e.dataTransfer.files[0];
                if (f) this.file = f;
            },
            onFileChange(e) {
                const f = e.target.files[0];
                if (f) this.file = f;
            },
            formatSize(bytes) {
                if (!bytes) return '';
                const kb = bytes / 1024;
                return kb > 1024 ? (kb / 1024).toFixed(1) + ' MB' : kb.toFixed(0) + ' KB';
            },
            notify(type, message) {
                window.dispatchEvent(new CustomEvent('toast', { detail: { type, message } }));
            },

            analyze() {
                if (!this.file) return;
                this.analyzing = true;

                const formData = new FormData();
                formData.append('arquivo', this.file);

                axios.post('{{ route('importacao.preview') }}', formData)
                    .then((res) => {
                        this.headersFound = res.data.headersFound;
                        this.missingHeaders = res.data.missingHeaders;
                        this.totalLinhas = res.data.totalLinhas;
                        this.invalidCount = res.data.invalidCount;
                        this.duplicateCount = res.data.duplicateCount;
                        this.previewRows = res.data.amostra;
                        this.step = 2;
                    })
                    .catch((err) => {
                        const msg = err.response?.data?.erro
                            || err.response?.data?.errors?.arquivo?.[0]
                            || 'Não foi possível analisar o arquivo.';
                        this.notify('error', msg);
                    })
                    .finally(() => { this.analyzing = false; });
            },

            confirmImport() {
                if (!this.file) return;
                this.processing = true;

                const formData = new FormData();
                formData.append('arquivo', this.file);

                axios.post('{{ route('importacao.store') }}', formData)
                    .then((res) => {
                        window.location.href = res.data.redirect;
                    })
                    .catch((err) => {
                        const msg = err.response?.data?.errors?.arquivo?.[0]
                            || 'Não foi possível concluir a importação.';
                        this.notify('error', msg);
                        this.processing = false;
                    });
            },
        };
    }
</script>
@endpush
