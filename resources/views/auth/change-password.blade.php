@extends('layouts.app')

@section('title', 'Ubah Kata Sandi')

@section('content')
<div class="max-w-md mx-auto bg-white rounded shadow p-6">
    <h1 class="text-2xl font-bold mb-4">Ubah Kata Sandi</h1>

    <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label for="current_password" class="block font-medium">Kata Sandi Saat Ini</label>
            <input type="password" id="current_password" name="current_password" required
                class="w-full border rounded px-3 py-2 mt-1">
            @error('current_password')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password" class="block font-medium">Kata Sandi Baru (Min. 8 Karakter)</label>
            <input type="password" id="password" name="password" required
                class="w-full border rounded px-3 py-2 mt-1">
            @error('password')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="block font-medium">Ulangi Kata Sandi Baru</label>
            <input type="password" id="password_confirmation" name="password_confirmation" required
                class="w-full border rounded px-3 py-2 mt-1">
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="px-5 py-2 bg-blue-700 text-white rounded">Simpan Password</button>
            <a href="{{ route('dashboard') }}" class="px-5 py-2 bg-gray-300 text-gray-800 rounded">Batal</a>
        </div>
    </form>
</div>
@endsection