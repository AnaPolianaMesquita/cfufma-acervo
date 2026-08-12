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
