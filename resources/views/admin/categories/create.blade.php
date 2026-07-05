@extends('layouts.admin')

@section('admin-content')
<h1 class="text-2xl font-bold mb-4">Nueva categoría</h1>

<div class="bg-base-100 rounded-box shadow p-6">
    <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.categories._form')
    </form>
</div>
@endsection