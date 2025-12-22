@extends('layouts.app')

@section('title', 'Edit SDM')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Edit SDM</h5>
    </div>

    <div class="card-body">
        <form action="{{ route('sdm.update', $sdm->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="nama" class="form-control" required value="{{ old('nama', $sdm->nama) }}">
                @error('nama') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group mt-3">
                <label>Peran</label>
                <input type="text" name="peran" class="form-control" required value="{{ old('peran', $sdm->peran) }}">
                @error('peran') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group mt-3">
                <label>Nomor Telepon</label>
                <input type="text" name="nomor_telepon" class="form-control"
                       value="{{ old('nomor_telepon', $sdm->nomor_telepon) }}">
                @error('nomor_telepon') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group mt-3">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control">{{ old('alamat', $sdm->alamat) }}</textarea>
                @error('alamat') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="mt-4">
                <button class="btn btn-maroon">Update</button>
                <a href="{{ route('sdm.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
