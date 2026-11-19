{{-- Campos compartilhados entre create e edit. Variável opcional: $leitor --}}
@php($l = $leitor ?? null)

<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label" for="nome">Nome completo *</label>
        <input id="nome" name="nome" value="{{ old('nome', $l?->nome) }}"
               class="form-control @error('nome') is-invalid @enderror">
    </div>

    <div class="col-md-4">
        <label class="form-label" for="cpf">CPF *</label>
        <input id="cpf" name="cpf" value="{{ old('cpf', $l?->cpf) }}" placeholder="000.000.000-00"
               class="form-control @error('cpf') is-invalid @enderror">
    </div>

    <div class="col-md-8">
        <label class="form-label" for="email">E-mail *</label>
        <input id="email" type="email" name="email" value="{{ old('email', $l?->email) }}"
               class="form-control @error('email') is-invalid @enderror">
    </div>

    <div class="col-md-4">
        <label class="form-label" for="telefone">Telefone *</label>
        <input id="telefone" name="telefone" value="{{ old('telefone', $l?->telefone) }}"
               class="form-control @error('telefone') is-invalid @enderror">
    </div>
</div>

<div class="mt-4">
    <button class="btn btn-primary">Salvar</button>
    <a class="btn btn-outline-secondary" href="{{ route('leitores.index') }}">Cancelar</a>
</div>
