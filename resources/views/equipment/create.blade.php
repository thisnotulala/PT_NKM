@extends('layouts.app')
@section('title', 'Tambah Equipment')

@section('content')
<div class="card">
  <div class="card-header"><h5>Tambah Equipment</h5></div>
  <div class="card-body">
    <form action="{{ route('equipment.store') }}" method="POST" autocomplete="off">
      @csrf

      <div class="form-group mt-3">
        <label>Nama Alat</label>
        <input type="text"
               name="nama_alat"
               class="form-control @error('nama_alat') is-invalid @enderror"
               value="{{ old('nama_alat') }}"
               autocomplete="off">
        @error('nama_alat')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-group mt-3">
        <label>Satuan</label>
        <select name="satuan_id"
                class="form-control @error('satuan_id') is-invalid @enderror">
          <option value="">-- pilih satuan --</option>
          @foreach($satuans as $s)
            <option value="{{ $s->id }}" {{ old('satuan_id') == $s->id ? 'selected' : '' }}>
              {{ $s->nama_satuan }}
            </option>
          @endforeach
        </select>
        @error('satuan_id')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted">Kalau satuan belum ada, buat dulu di menu Master Satuan.</small>
      </div>

      <div class="form-group mt-3">
        <label>Stok</label>
        <input type="number"
               name="stok"
               class="form-control @error('stok') is-invalid @enderror"
               min="0"
               value="{{ old('stok', 0) }}"
               autocomplete="off">
        @error('stok')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-group mt-3">
        <label>Kondisi</label>
        <input type="text"
               name="kondisi"
               class="form-control @error('kondisi') is-invalid @enderror"
               value="{{ old('kondisi', 'baik') }}"
               autocomplete="off">
        @error('kondisi')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <small class="text-muted">Contoh: baik / rusak ringan / rusak berat</small>
      </div>

      <div class="mt-4">
        <button class="btn btn-maroon">Simpan</button>
        <a href="{{ route('equipment.index') }}" class="btn btn-secondary">Kembali</a>
      </div>
    </form>
  </div>
</div>
@endsection
