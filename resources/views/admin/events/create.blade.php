@extends('layouts.admin')

@section('admin-content')
<h1 class="text-2xl font-bold mb-6">Nuevo evento</h1>

<form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data">
    @include('admin.events._form')
</form>
@endsection