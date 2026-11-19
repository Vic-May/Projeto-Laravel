@extends('layouts.app')

@section('title', 'Leitores')

@section('content')
<h1>Leitores</h1>

<a class="btn btn-primary mb-3" href="{{ route('leitores.create') }}">Novo leitor</a>

<form method="GET" action="{{ route('leitores.index') }}" class="row g-2 mb-3">
    <div class="col-md-6">
        <input name="busca" value="{{ $busca }}" class="form-control" placeholder="Nome do leitor">
    </div>
    <div class="col-auto">
        <button class="btn btn-secondary">Pesquisar</button>
        @if($busca)
            <a class="btn btn-outline-secondary" href="{{ route('leitores.index') }}">Limpar</a>
        @endif
    </div>
</form>

<table class="table table-striped bg-white">
    <thead>
        <tr><th>Nome</th><th>CPF</th><th>E-mail</th><th>Telefone</th><th>Ações</th></tr>
    </thead>
    <tbody>
    @forelse($leitores as $leitor)
        <tr>
            <td>{{ $leitor->nome }}</td>
            <td>{{ $leitor->cpf }}</td>
            <td>{{ $leitor->email }}</td>
            <td>{{ $leitor->telefone }}</td>
            <td class="text-nowrap">
                <a class="btn btn-sm btn-warning" href="{{ route('leitores.edit', $leitor) }}">Editar</a>
                <form method="POST" action="{{ route('leitores.destroy', $leitor) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Excluir este leitor?')">Excluir</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="5">Nenhum leitor encontrado.</td></tr>
    @endforelse
    </tbody>
</table>

{{ $leitores->links() }}
@endsection
