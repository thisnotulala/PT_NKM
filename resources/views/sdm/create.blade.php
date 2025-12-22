@extends('layouts.app')

@section('title', 'Tambah SDM')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Tambah SDM</h5>
    </div>

    <div class="card-body">
        <form action="{{ route('sdm.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="nama" class="form-control" required value="{{ old('nama') }}">
                @error('nama') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group mt-3">
                <label>Peran</label>
                <input type="text" name="peran" class="form-control" required value="{{ old('peran') }}">
                @error('peran') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group mt-3">
                <label>Nomor Telepon</label>
                <input type="text" name="nomor_telepon" class="form-control" value="{{ old('nomor_telepon') }}">
                @error('nomor_telepon') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group mt-3">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control">{{ old('alamat') }}</textarea>
                @error('alamat') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mt-4">
                <button class="btn btn-maroon">Simpan</button>
                <a href="{{ route('sdm.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
