@extends('layouts.app')

@section('title', 'Tambah Client')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Tambah Client</h5>
    </div>

    <div class="card-body">
        <form action="{{ route('client.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label>Nama Client</label>
                <input type="text" name="nama" class="form-control" required value="{{ old('nama') }}">
                @error('nama') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group mt-3">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control">{{ old('alamat') }}</textarea>
                @error('alamat') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group mt-3">
                <label>Nomor Telepon</label>
                <input type="text"
                        name="nomor_telepon"
                        class="form-control"
                        inputmode="numeric"
                        pattern="[0-9]*"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                        placeholder="Contoh: 08123456789"
                        value="{{ old('nomor_telepon') }}"
                        required>

                @error('nomor_telepon')
                  <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mt-4">
                <button class="btn btn-maroon">Simpan</button>
                <a href="{{ route('client.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
