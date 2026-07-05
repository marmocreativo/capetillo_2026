@extends('layouts.admin')

@section('admin-content')
<h1 class="text-2xl font-bold mb-4">Editar categoría</h1>

<div class="bg-base-100 rounded-box shadow p-6">
    <form action="{{ route('admin.categories.update', $category) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        @include('admin.categories._form')
    </form>
</div>
@endsection