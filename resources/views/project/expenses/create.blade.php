@extends('layouts.app')
@section('title','Tambah Pengeluaran')

@section('content')
<div class="card">
  <div class="card-header">
    <h5>Tambah Pengeluaran - {{ $project->nama_proyek }}</h5>
  </div>

  <div class="card-body">
    @if($errors->any())
      <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('project.expenses.store', $project->id) }}" method="POST" enctype="multipart/form-data">
      @csrf

      <div class="form-group">
        <label>Tanggal</label>
        <input type="date" name="tanggal" class="form-control" required
               value="{{ old('tanggal', date('Y-m-d')) }}">
        <small class="text-muted">Rentang proyek: {{ $project->tanggal_mulai }} s/d {{ $project->tanggal_selesai }}</small>
      </div>

      <div class="form-group mt-3">
        <label>Kategori</label>
        <select name="kategori" class="form-control" required>
          @php
            $kats = ['Material','SDM','Equipment','Operasional','Lainnya'];
          @endphp
          <option value="">-- pilih kategori --</option>
          @foreach($kats as $k)
            <option value="{{ $k }}" {{ old('kategori') == $k ? 'selected' : '' }}>{{ $k }}</option>
          @endforeach
        </select>
      </div>

      <div class="form-group mt-3">
        <label>Nominal</label>
        <input type="number" name="nominal" class="form-control" required min="1"
               value="{{ old('nominal') }}" placeholder="contoh: 50000">
      </div>

      <div class="form-group mt-3">
        <label>Keterangan (opsional)</label>
        <input type="text" name="keterangan" class="form-control"
               value="{{ old('keterangan') }}" placeholder="misal: beli semen 10 sak">
      </div>

      <div class="form-group mt-3">
        <label>SDM (opsional)</label>
        <select name="sdm_id" class="form-control">
          <option value="">-- tidak ada --</option>
          @foreach($sdms as $s)
            <option value="{{ $s->id }}" {{ old('sdm_id') == $s->id ? 'selected' : '' }}>
              {{ $s->nama }} ({{ $s->peran }})
            </option>
          @endforeach
        </select>
      </div>

      <div class="form-group mt-3">
        <label>Equipment (opsional)</label>
        <select name="equipment_id" class="form-control">
          <option value="">-- tidak ada --</option>
          @foreach($equipment as $eq)
            <option value="{{ $eq->id }}" {{ old('equipment_id') == $eq->id ? 'selected' : '' }}>
              {{ $eq->nama_alat }} (Stok: {{ $eq->stok }})
            </option>
          @endforeach
        </select>
      </div>

      <div class="form-group mt-3">
        <label>Upload Bukti (opsional)</label>
        <input type="file" name="bukti" class="form-control" accept="image/png,image/jpeg,application/pdf">
        <small class="text-muted">Format: jpg/png/pdf. Maks 4MB.</small>
      </div>

      <div class="mt-4">
        <button class="btn btn-maroon">Simpan</button>
        <a href="{{ route('project.expenses.index', $project->id) }}" class="btn btn-secondary">Kembali</a>
      </div>
    </form>
  </div>
</div>
@endsection
