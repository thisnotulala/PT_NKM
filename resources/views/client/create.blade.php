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
                <input type="text" name="nama" class="form-control" required>
            </div>

            <div class="form-group mt-3">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control"></textarea>
            </div>

            <div class="form-group mt-3">
                <label>Nomor Telepon</label>
                <input type="text" name="nomor_telepon" class="form-control">
            </div>

            <div class="mt-4">
                <button class="btn btn-maroon">Simpan</button>
                <a href="{{ route('client.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
