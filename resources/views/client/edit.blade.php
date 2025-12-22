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
                       value="{{ $client->nama }}"
                       class="form-control" required>
            </div>

            <div class="form-group mt-3">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control">{{ $client->alamat }}</textarea>
            </div>

            <div class="form-group mt-3">
                <label>Nomor Telepon</label>
                <input type="text" name="nomor_telepon"
                       value="{{ $client->nomor_telepon }}"
                       class="form-control">
            </div>

            <div class="mt-4">
                <button class="btn btn-maroon">Update</button>
                <a href="{{ route('client.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
