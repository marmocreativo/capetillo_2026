@extends('layouts.admin')

@section('admin-content')
<h1 class="text-2xl font-bold mb-6">Nuevo roster</h1>

<form action="{{ route('admin.rosters.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @include('admin.rosters._form')
</form>
@endsection