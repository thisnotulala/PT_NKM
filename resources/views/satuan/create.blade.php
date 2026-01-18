@extends('layouts.app')
@section('title', 'Tambah Satuan')

@section('content')
<div class="card">
  <div class="card-header"><h5>Tambah Satuan</h5></div>
  <div class="card-body">

    {{-- ERROR VALIDASI --}}
    <form action="{{ route('satuan.store') }}" method="POST" autocomplete="off">
      @csrf

      <div class="form-group">
        <label>Nama Satuan</label>
        <input type="text"
               name="nama_satuan"
               class="form-control @error('nama_satuan') is-invalid @enderror"
               value="{{ old('nama_satuan') }}"
               placeholder="Contoh: Kg, Liter, m2">

        @error('nama_satuan')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mt-4">
        <button class="btn btn-maroon">Simpan</button>
        <a href="{{ route('satuan.index') }}" class="btn btn-secondary">Kembali</a>
      </div>
    </form>
  </div>
</div>
@endsection
