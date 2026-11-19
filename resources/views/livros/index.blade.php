@extends('layouts.app')

@section('title', 'Livros')

@section('content')
<h1>Livros</h1>

<a class="btn btn-primary mb-3" href="{{ route('livros.create') }}">Novo livro</a>

<form method="GET" action="{{ route('livros.index') }}" class="row g-2 mb-3">
    <div class="col-md-6">
        <input name="busca" value="{{ $busca }}" class="form-control" placeholder="Título ou autor">
    </div>
    <div class="col-auto">
        <button class="btn btn-secondary">Pesquisar</button>
        @if($busca)
            <a class="btn btn-outline-secondary" href="{{ route('livros.index') }}">Limpar</a>
        @endif
    </div>
</form>

<table class="table table-striped bg-white">
    <thead>
        <tr><th>Título</th><th>Autor</th><th>Editora</th><th>Ano</th><th>ISBN</th><th>Disponíveis</th><th>Ações</th></tr>
    </thead>
    <tbody>
    @forelse($livros as $livro)
        <tr>
            <td>{{ $livro->titulo }}</td>
            <td>{{ $livro->autor }}</td>
            <td>{{ $livro->editora }}</td>
            <td>{{ $livro->ano_publicacao }}</td>
            <td>{{ $livro->isbn }}</td>
            <td>{{ $livro->quantidade }}</td>
            <td class="text-nowrap">
                <a class="btn btn-sm btn-warning" href="{{ route('livros.edit', $livro) }}">Editar</a>
                <form method="POST" action="{{ route('livros.destroy', $livro) }}" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Excluir este livro?')">Excluir</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="7">Nenhum livro encontrado.</td></tr>
    @endforelse
    </tbody>
</table>

{{ $livros->links() }}
@endsection
