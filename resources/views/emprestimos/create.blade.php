@extends('layouts.app')

@section('title', 'Novo empréstimo')

@section('content')
<h1>Novo empréstimo</h1>

@if($livros->isEmpty())
    <div class="alert alert-warning">Não há livros com exemplares disponíveis no momento.</div>
@endif

<form method="POST" action="{{ route('emprestimos.store') }}">
    @csrf

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="leitor_id">Leitor *</label>
            <select id="leitor_id" name="leitor_id" class="form-select @error('leitor_id') is-invalid @enderror">
                <option value="">Selecione...</option>
                @foreach($leitores as $leitor)
                    <option value="{{ $leitor->id }}" @selected(old('leitor_id') == $leitor->id)>
                        {{ $leitor->nome }} ({{ $leitor->cpf }})
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label" for="livro_id">Livro *</label>
            <select id="livro_id" name="livro_id" class="form-select @error('livro_id') is-invalid @enderror">
                <option value="">Selecione...</option>
                @foreach($livros as $livro)
                    <option value="{{ $livro->id }}" @selected(old('livro_id') == $livro->id)>
                        {{ $livro->titulo }} — {{ $livro->autor }} ({{ $livro->quantidade }} disp.)
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label" for="data_emprestimo">Data do empréstimo *</label>
            <input id="data_emprestimo" type="date" name="data_emprestimo"
                   value="{{ old('data_emprestimo', today()->toDateString()) }}"
                   class="form-control @error('data_emprestimo') is-invalid @enderror">
        </div>

        <div class="col-md-3">
            <label class="form-label" for="data_devolucao_prevista">Devolução prevista *</label>
            <input id="data_devolucao_prevista" type="date" name="data_devolucao_prevista"
                   value="{{ old('data_devolucao_prevista', today()->addDays(14)->toDateString()) }}"
                   class="form-control @error('data_devolucao_prevista') is-invalid @enderror">
        </div>
    </div>

    <div class="mt-4">
        <button class="btn btn-primary">Confirmar empréstimo</button>
        <a class="btn btn-outline-secondary" href="{{ route('emprestimos.index') }}">Cancelar</a>
    </div>
</form>
@endsection
