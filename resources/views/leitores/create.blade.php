@extends('layouts.app')

@section('title', 'Novo leitor')

@section('content')
<h1>Novo leitor</h1>

<form method="POST" action="{{ route('leitores.store') }}">
    @csrf
    @include('leitores._form')
</form>
@endsection
