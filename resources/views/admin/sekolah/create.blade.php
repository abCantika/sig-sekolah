@extends('layouts.app')

@section('title', 'Tambah Sekolah')

@section('content')
<div class="max-w-6xl mx-auto">
    <h1 class="text-2xl font-bold mb-4">Tambah Sekolah</h1>

    <form action="{{ route('dinas.sekolah.store') }}" method="POST">
        @csrf
        @include('admin.sekolah._form')
    </form>
</div>
@endsection