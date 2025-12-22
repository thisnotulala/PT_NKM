@extends('layouts.app')
@section('title', 'Edit Satuan')

@section('content')
<div class="card">
  <div class="card-header"><h5>Edit Satuan</h5></div>
  <div class="card-body">
    <form action="{{ route('satuan.update', $satuan->id) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="form-group">
        <label>Nama Satuan</label>
        <input type="text" name="nama_satuan" class="form-control" required
               value="{{ old('nama_satuan', $satuan->nama_satuan) }}">
        @error('nama_satuan') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      <div class="mt-4">
        <button class="btn btn-maroon">Update</button>
        <a href="{{ route('satuan.index') }}" class="btn btn-secondary">Kembali</a>
      </div>
    </form>
  </div>
</div>
@endsection
