<div class="space-y-5">
    <div>
        <label class="block text-sm font-medium text-ink mb-1.5">Foto da espécie</label>

        @if (!empty($isolado['imagem']))
            <div class="flex items-center gap-4 mb-3">
                <img src="{{ $isolado->imagemUrl() }}" alt="Foto de {{ $isolado['codigo'] }}" class="w-24 h-24 object-cover rounded-lg border border-slate-200">
                <label class="inline-flex items-center gap-2 text-sm text-muted">
                    <input type="checkbox" name="remover_imagem" value="1" class="rounded border-slate-300 text-brand focus:ring-brand">
                    Remover foto atual
                </label>
            </div>
        @endif

        <input
            id="imagem"
            name="imagem"
            type="file"
            accept="image/*"
            class="block w-full text-sm text-ink file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-brand-light file:text-brand hover:file:opacity-90 {{ $errors->first('imagem') ? 'border border-red-300 rounded-lg' : '' }}"
        >

        @if ($errors->first('imagem'))
            <p class="mt-1.5 text-xs text-red-600">{{ $errors->first('imagem') }}</p>
        @else
            <p class="mt-1.5 text-xs text-muted">JPG, PNG ou WEBP, até 4MB.</p>
        @endif
    </div>

    <div>
        <label for="descricao" class="block text-sm font-medium text-ink mb-1.5">Descrição</label>
        <textarea
            id="descricao"
            name="descricao"
            rows="4"
            placeholder="Descreva características da espécie para divulgação ao público (ciência cidadã)."
            class="block w-full rounded-lg border px-3 py-2.5 text-sm text-ink placeholder-slate-400 shadow-sm transition-colors focus:outline-none focus:ring-1 {{ $errors->first('descricao') ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-slate-300 focus:border-brand focus:ring-brand' }}"
        >{{ old('descricao', $isolado['descricao'] ?? '') }}</textarea>

        @if ($errors->first('descricao'))
            <p class="mt-1.5 text-xs text-red-600">{{ $errors->first('descricao') }}</p>
        @endif
    </div>
</div>

<div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
    <x-input
        label="Código"
        name="codigo"
        required
        placeholder="Ex.: ISO-065"
        value="{{ old('codigo', $isolado['codigo'] ?? '') }}"
        :error="$errors->first('codigo')"
    />

    <x-input
        label="Autor"
        name="autor"
        required
        placeholder="Nome do responsável pela coleta"
        value="{{ old('autor', $isolado['autor'] ?? '') }}"
        :error="$errors->first('autor')"
    />

    <x-input
        label="Gênero"
        name="genero"
        required
        list="lista-generos"
        placeholder="Ex.: Aspergillus"
        value="{{ old('genero', $isolado['genero'] ?? '') }}"
        :error="$errors->first('genero')"
    />

    <x-input
        label="Espécie"
        name="especie"
        required
        placeholder="Ex.: niger"
        value="{{ old('especie', $isolado['especie'] ?? '') }}"
        :error="$errors->first('especie')"
        hint="Informe apenas o epíteto específico."
    />

    <x-input
        label="Origem"
        name="origem"
        placeholder="Ex.: Solo, água, planta..."
        value="{{ old('origem', $isolado['origem'] ?? '') }}"
        :error="$errors->first('origem')"
    />

    <x-input
        label="Meio de cultivo"
        name="meio_cultivo"
        list="lista-meios-cultivo"
        placeholder="Ex.: Ágar Sabouraud"
        value="{{ old('meio_cultivo', $isolado['meio_cultivo'] ?? '') }}"
        :error="$errors->first('meio_cultivo')"
    />

    <x-input
        label="Data de coleta"
        name="data"
        type="date"
        value="{{ old('data', optional($isolado['data'] ?? null)->format('Y-m-d')) }}"
        :error="$errors->first('data')"
    />

    <x-input
        label="Conservação"
        name="conservacao"
        list="lista-conservacoes"
        placeholder="Ex.: Liofilização"
        value="{{ old('conservacao', $isolado['conservacao'] ?? '') }}"
        :error="$errors->first('conservacao')"
    />

    <x-input
        label="Local de armazenamento"
        name="local"
        list="lista-locais"
        placeholder="Ex.: Freezer -20°C"
        value="{{ old('local', $isolado['local'] ?? '') }}"
        :error="$errors->first('local')"
    />

    <x-input
        label="Armazenamento"
        name="armazenamento"
        placeholder="Ex.: Criotubo, tubo de ensaio..."
        value="{{ old('armazenamento', $isolado['armazenamento'] ?? '') }}"
        :error="$errors->first('armazenamento')"
    />
</div>

<datalist id="lista-generos">
    @foreach ($generos as $g)
        <option value="{{ $g }}"></option>
    @endforeach
</datalist>

<datalist id="lista-meios-cultivo">
    @foreach ($meiosCultivo as $m)
        <option value="{{ $m }}"></option>
    @endforeach
</datalist>

<datalist id="lista-conservacoes">
    @foreach ($conservacoes as $c)
        <option value="{{ $c }}"></option>
    @endforeach
</datalist>

<datalist id="lista-locais">
    @foreach ($locais as $l)
        <option value="{{ $l }}"></option>
    @endforeach
</datalist>
