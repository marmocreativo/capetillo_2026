@extends('layouts.admin')

@section('admin-content')
<h1 class="text-2xl font-bold mb-6">Nuevo slide</h1>

<form action="{{ route('admin.home-slides.store') }}" method="POST" enctype="multipart/form-data">
    @include('admin.home-slides._form')
</form>
@endsection