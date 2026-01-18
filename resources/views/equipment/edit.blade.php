@extends('layouts.app')
@section('title', 'Edit Equipment')

@section('content')
<div class="card">
  <div class="card-header"><h5>Edit Equipment</h5></div>
  <div class="card-body">
    <form action="{{ route('equipment.update', $equipment->id) }}" method="POST" autocomplete="off">
      @csrf
      @method('PUT')

      <div class="form-group mt-3">
        <label>Nama Alat</label>
        <input type="text"
               name="nama_alat"
               class="form-control @error('nama_alat') is-invalid @enderror"
               value="{{ old('nama_alat', $equipment->nama_alat) }}"
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
            <option value="{{ $s->id }}"
              {{ old('satuan_id', $equipment->satuan_id) == $s->id ? 'selected' : '' }}>
              {{ $s->nama_satuan }}
            </option>
          @endforeach
        </select>
        @error('satuan_id')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="form-group mt-3">
        <label>Stok</label>
        <input type="number"
               name="stok"
               class="form-control @error('stok') is-invalid @enderror"
               min="0"
               value="{{ old('stok', $equipment->stok) }}"
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
               value="{{ old('kondisi', $equipment->kondisi) }}"
               autocomplete="off">
        @error('kondisi')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <div class="mt-4">
        <button class="btn btn-maroon">Update</button>
        <a href="{{ route('equipment.index') }}" class="btn btn-secondary">Kembali</a>
      </div>
    </form>
  </div>
</div>
@endsection
