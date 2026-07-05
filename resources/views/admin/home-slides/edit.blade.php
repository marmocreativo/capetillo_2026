@extends('layouts.admin')

@section('admin-content')
<h1 class="text-2xl font-bold mb-6">Editar slide</h1>

<form action="{{ route('admin.home-slides.update', $homeSlide) }}" method="POST" enctype="multipart/form-data">
    @method('PUT')
    @include('admin.home-slides._form')
</form>
@endsection