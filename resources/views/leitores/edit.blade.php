@extends('layouts.app')

@section('title', 'Editar leitor')

@section('content')
<h1>Editar leitor</h1>

<form method="POST" action="{{ route('leitores.update', $leitor) }}">
    @csrf
    @method('PUT')
    @include('leitores._form', ['leitor' => $leitor])
</form>
@endsection
