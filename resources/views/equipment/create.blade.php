@extends('layouts.app')
@section('title', 'Tambah Equipment')

@section('content')
<div class="card">
  <div class="card-header"><h5>Tambah Equipment</h5></div>
  <div class="card-body">
    <form action="{{ route('equipment.store') }}" method="POST">
      @csrf

      <div class="form-group mt-3">
        <label>Nama Alat</label>
        <input type="text" name="nama_alat" class="form-control" required value="{{ old('nama_alat') }}">
        @error('nama_alat') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      <div class="form-group mt-3">
        <label>Satuan</label>
        <select name="satuan_id" class="form-control" required>
          <option value="">-- pilih satuan --</option>
          @foreach($satuans as $s)
            <option value="{{ $s->id }}" {{ old('satuan_id') == $s->id ? 'selected' : '' }}>
              {{ $s->nama_satuan }}
            </option>
          @endforeach
        </select>
        @error('satuan_id') <small class="text-danger">{{ $message }}</small> @enderror
        <small class="text-muted">Kalau satuan belum ada, buat dulu di menu Master Satuan.</small>
      </div>

      <div class="form-group mt-3">
        <label>Stok</label>
        <input type="number" name="stok" class="form-control" min="0" required value="{{ old('stok', 0) }}">
        @error('stok') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      <div class="form-group mt-3">
        <label>Kondisi</label>
        <input type="text" name="kondisi" class="form-control" required value="{{ old('kondisi', 'baik') }}">
        @error('kondisi') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      <div class="mt-4">
        <button class="btn btn-maroon">Simpan</button>
        <a href="{{ route('equipment.index') }}" class="btn btn-secondary">Kembali</a>
      </div>
    </form>
  </div>
</div>
@endsection
