@extends('layouts.admin')

@section('admin-content')
<h1 class="text-2xl font-bold mb-6">Editar roster</h1>

<form action="{{ route('admin.rosters.update', $roster) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    @include('admin.rosters._form')
</form>
@endsection