@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<h1 class="text-2xl font-bold mb-1">Selamat datang, {{ $user->name }}</h1>
<p class="text-gray-600 mb-4">Masuk sebagai {{ $user->roleLabel() }}</p>

@if ($user->isAdmin())
    <div class="grid md:grid-cols-3 gap-4">
        @foreach ($jumlahPerRole as $label => $jumlah)
            <div class="bg-white rounded-md shadow p-4">
                <p class="text-3xl font-bold text-blue-800 mb-1">{{ $jumlah }}</p>
                <p class="text-gray-600 font-medium">Akun {{ $label }}</p>
            </div>
        @endforeach
    </div>
@elseif ($user->isDinas())
    <div class="bg-white rounded-md shadow p-4 w-72">
        <p class="text-3xl font-bold text-blue-800 mb-1">{{ $jumlahSekolah }}</p>
        <p class="text-gray-600 font-medium">Sekolah terdata</p>
    </div>
@elseif ($sekolah)
    <div class="bg-white rounded-md shadow p-4 max-w-lg">
        <p class="text-xl font-bold text-gray-800 mb-1">{{ $sekolah->nama }}</p>
        <p class="text-gray-600 mb-3">NPSN {{ $sekolah->npsn }} - {{ $sekolah->jenjang }} {{ $sekolah->status }}</p>
        
        <p class="text-sm font-medium text-gray-700">
            Kelengkapan profil: <strong>{{ $kelengkapan }}%</strong>
        </p>
        <div class="w-full bg-gray-200 rounded-full h-2.5 mt-2">
            <div class="bg-blue-700 h-2.5 rounded-full" style="width: {{ $kelengkapan }}%"></div>
        </div>
    </div>
@else
    <div class="p-4 rounded-md bg-yellow-100 text-yellow-800 border border-yellow-200">
        Akun Anda belum ditautkan ke sekolah. Hubungi admin.
    </div>
@endif
@endsection