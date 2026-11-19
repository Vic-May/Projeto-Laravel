@extends('layouts.app')

@section('title', 'Empréstimos')

@section('content')
<h1>Empréstimos</h1>

<a class="btn btn-primary mb-3" href="{{ route('emprestimos.create') }}">Novo empréstimo</a>

<form method="GET" action="{{ route('emprestimos.index') }}" class="row g-2 align-items-center mb-3">
    <div class="col-md-3">
        <select name="status" class="form-select">
            <option value="">Todas as situações</option>
            <option value="Em andamento" @selected(request('status') === 'Em andamento')>Em andamento</option>
            <option value="Devolvido" @selected(request('status') === 'Devolvido')>Devolvido</option>
        </select>
    </div>
    <div class="col-auto">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" name="atrasados" value="1" id="atrasados"
                   @checked(request()->boolean('atrasados'))>
            <label class="form-check-label" for="atrasados">Somente atrasados</label>
        </div>
    </div>
    <div class="col-auto">
        <button class="btn btn-secondary">Filtrar</button>
        <a class="btn btn-outline-secondary" href="{{ route('emprestimos.index') }}">Limpar</a>
    </div>
</form>

<table class="table table-striped bg-white">
    <thead>
        <tr>
            <th>Leitor</th><th>Livro</th><th>Empréstimo</th><th>Devolução prevista</th>
            <th>Devolvido em</th><th>Situação</th><th>Ações</th>
        </tr>
    </thead>
    <tbody>
    @forelse($emprestimos as $emprestimo)
        <tr>
            <td>{{ $emprestimo->leitor->nome }}</td>
            <td>{{ $emprestimo->livro->titulo }}</td>
            <td>{{ $emprestimo->data_emprestimo->format('d/m/Y') }}</td>
            <td>{{ $emprestimo->data_devolucao_prevista->format('d/m/Y') }}</td>
            <td>{{ $emprestimo->data_devolucao?->format('d/m/Y') ?? '—' }}</td>
            <td>
                @if($emprestimo->status === 'Devolvido')
                    <span class="badge text-bg-success">Devolvido</span>
                @elseif($emprestimo->atrasado)
                    <span class="badge text-bg-danger">Em andamento (atrasado)</span>
                @else
                    <span class="badge text-bg-info">Em andamento</span>
                @endif
            </td>
            <td>
                @if($emprestimo->status === 'Em andamento')
                    <form method="POST" action="{{ route('emprestimos.devolver', $emprestimo) }}">
                        @csrf
                        @method('PATCH')
                        <button class="btn btn-sm btn-success">Registrar Devolução</button>
                    </form>
                @endif
            </td>
        </tr>
    @empty
        <tr><td colspan="7">Nenhum empréstimo encontrado.</td></tr>
    @endforelse
    </tbody>
</table>

{{ $emprestimos->links() }}
@endsection
