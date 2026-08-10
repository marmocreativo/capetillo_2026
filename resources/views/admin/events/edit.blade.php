@extends('layouts.admin')

@section('admin-content')
<h1 class="text-2xl font-bold mb-6">Editar evento</h1>

<form action="{{ route('admin.events.update', $event) }}" method="POST" enctype="multipart/form-data">
    @method('PUT')
    @include('admin.events._form')
</form>
@endsection