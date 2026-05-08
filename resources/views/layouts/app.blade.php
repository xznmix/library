@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-2xl font-bold mb-4">Dashboard Admin</h1>
                <p>Selamat datang, {{ auth()->user()->name }}!</p>
                <p>Role: {{ auth()->user()->role }}</p>
                <p>Email: {{ auth()->user()->email }}</p>
                
                <!-- Statistics -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 p-4 rounded-lg">
                        <h3 class="font-semibold">Total Pengguna</h3>
                        <p class="text-2xl">{{ $totalUsers ?? 0 }}</p>
                    </div>
                    <div class="bg-green-50 p-4 rounded-lg">
                        <h3 class="font-semibold">Total Anggota</h3>
                        <p class="text-2xl">{{ $totalAnggota ?? 0 }}</p>
                    </div>
                    <div class="bg-yellow-50 p-4 rounded-lg">
                        <h3 class="font-semibold">Petugas</h3>
                        <p class="text-2xl">{{ $totalPetugas ?? 0 }}</p>
                    </div>
                    <div class="bg-red-50 p-4 rounded-lg">
                        <h3 class="font-semibold">Admin</h3>
                        <p class="text-2xl">{{ $totalAdmin ?? 0 }}</p>
                    </div>
                </div>
                
                <div class="mt-6">
                    <a href="{{ '#'" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
                        Kelola Pengguna
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection