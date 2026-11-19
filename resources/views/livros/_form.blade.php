{{-- Campos compartilhados entre create e edit. Variável opcional: $livro --}}
@php($l = $livro ?? null)

<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label" for="titulo">Título *</label>
        <input id="titulo" name="titulo" value="{{ old('titulo', $l?->titulo) }}"
               class="form-control @error('titulo') is-invalid @enderror">
    </div>

    <div class="col-md-4">
        <label class="form-label" for="isbn">ISBN *</label>
        <input id="isbn" name="isbn" value="{{ old('isbn', $l?->isbn) }}"
               class="form-control @error('isbn') is-invalid @enderror">
    </div>

    <div class="col-md-6">
        <label class="form-label" for="autor">Autor *</label>
        <input id="autor" name="autor" value="{{ old('autor', $l?->autor) }}"
               class="form-control @error('autor') is-invalid @enderror">
    </div>

    <div class="col-md-6">
        <label class="form-label" for="editora">Editora</label>
        <input id="editora" name="editora" value="{{ old('editora', $l?->editora) }}"
               class="form-control @error('editora') is-invalid @enderror">
    </div>

    <div class="col-md-3">
        <label class="form-label" for="ano_publicacao">Ano de publicação</label>
        <input id="ano_publicacao" type="number" name="ano_publicacao"
               value="{{ old('ano_publicacao', $l?->ano_publicacao) }}"
               class="form-control @error('ano_publicacao') is-invalid @enderror">
    </div>

    <div class="col-md-3">
        <label class="form-label" for="quantidade">Quantidade disponível *</label>
        <input id="quantidade" type="number" min="0" name="quantidade"
               value="{{ old('quantidade', $l?->quantidade ?? 0) }}"
               class="form-control @error('quantidade') is-invalid @enderror">
    </div>
</div>

<div class="mt-4">
    <button class="btn btn-primary">Salvar</button>
    <a class="btn btn-outline-secondary" href="{{ route('livros.index') }}">Cancelar</a>
</div>
