@extends('layouts.app')

@section('title', 'Editar livro')

@section('content')
<h1>Editar livro</h1>

<form method="POST" action="{{ route('livros.update', $livro) }}">
    @csrf
    @method('PUT')
    @include('livros._form', ['livro' => $livro])
</form>
@endsection
