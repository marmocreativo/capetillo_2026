@extends('layouts.admin')

@section('admin-content')
<h1 class="text-2xl font-bold mb-4">Nuevo talento</h1>

<div class="bg-base-100 rounded-box shadow p-6">
    <form action="{{ route('admin.talents.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @include('admin.talents._form')
    </form>
</div>
@endsection