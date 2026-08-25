@extends('layouts.public')

@section('title', 'Quem somos - MicoNIBA')

@section('content')

<div class="max-w-3xl mx-auto space-y-8">
    <div>
        <h1 class="text-2xl font-bold text-ink">Quem somos</h1>
        <p class="text-muted mt-1">Conheça o MicoNIBA e o acervo micológico do NIBA/UFMA.</p>
    </div>

    <x-card class="space-y-4 leading-relaxed text-slate-700">
        <p>
            O <strong class="text-ink">MicoNIBA</strong> é um banco de dados online desenvolvido para a organização e
            informatização do acervo de fungos vinculado ao Núcleo de Imunologia Básica e Aplicada (NIBA) da
            Universidade Federal do Maranhão (UFMA). A plataforma reúne informações associadas aos isolados fúngicos,
            como gênero, espécie, descrição e ilustração. O sistema permite o cadastro, consulta, edição e organização
            dos registros da coleção de fungos.
        </p>
        <p>
            Dessa forma, o MicoNIBA contribui para a organização e preservação das informações do acervo, além de
            ampliar o acesso aos dados e facilitar sua utilização pela comunidade científica, podendo auxiliar no
            desenvolvimento de pesquisas e na produção de novos conhecimentos sobre a diversidade fúngica.
        </p>
    </x-card>
</div>

@endsection
