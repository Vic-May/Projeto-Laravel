@extends('layouts.app')

@section('title', 'Novo livro')

@section('content')
<h1>Novo livro</h1>

<form method="POST" action="{{ route('livros.store') }}">
    @csrf
    @include('livros._form')
</form>
@endsection
