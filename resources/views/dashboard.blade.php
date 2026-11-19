@extends('layouts.app')

@section('title', 'Painel')

@section('content')
<h1 class="mb-4">Painel da biblioteca</h1>

<div class="row g-3">
    <div class="col-md-3">
        <div class="card border-primary h-100">
            <div class="card-body">
                <h6 class="text-muted">Livros cadastrados</h6>
                <h2 class="text-primary">{{ $totalLivros }}</h2>
                <a href="{{ route('livros.index') }}" class="stretched-link small">Ver livros</a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-success h-100">
            <div class="card-body">
                <h6 class="text-muted">Leitores cadastrados</h6>
                <h2 class="text-success">{{ $totalLeitores }}</h2>
                <a href="{{ route('leitores.index') }}" class="stretched-link small">Ver leitores</a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-info h-100">
            <div class="card-body">
                <h6 class="text-muted">Empréstimos em andamento</h6>
                <h2 class="text-info">{{ $emAndamento }}</h2>
                <a href="{{ route('emprestimos.index', ['status' => 'Em andamento']) }}" class="stretched-link small">Ver empréstimos</a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-danger h-100">
            <div class="card-body">
                <h6 class="text-muted">Empréstimos atrasados</h6>
                <h2 class="text-danger">{{ $atrasados }}</h2>
                <a href="{{ route('emprestimos.index', ['atrasados' => 1]) }}" class="stretched-link small">Ver atrasados</a>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a class="btn btn-primary" href="{{ route('emprestimos.create') }}">Registrar empréstimo</a>
</div>
@endsection
