@extends('layouts.app')

@section('title', 'Edit Client')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Edit Client</h5>
    </div>

    <div class="card-body">
        <form action="{{ route('client.update', $client->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Nama Client</label>
                <input type="text" name="nama"
                       value="{{ old('nama', $client->nama) }}"
                       class="form-control" required>
                @error('nama') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="form-group mt-3">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control">{{ old('alamat', $client->alamat) }}</textarea>
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
                        value="{{ old('nomor_telepon', $client->nomor_telepon) }}"
                        required>

                @error('nomor_telepon')
                  <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="mt-4">
                <button class="btn btn-maroon">Update</button>
                <a href="{{ route('client.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
