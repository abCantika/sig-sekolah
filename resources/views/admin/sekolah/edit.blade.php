@extends('layouts.app')

@section('title', 'Ubah Sekolah')

@section('content')
<h1 class="text-2xl font-bold mb-1">{{ $sekolah->nama }}</h1>
@include('sekolah._tab', ['sekolah' => $sekolah])

<form action="{{ route('sekolah.update', $sekolah->id) }}" method="POST">
    @csrf
    @method('PUT')
    @include('admin.sekolah._form')
</form>
@endsection