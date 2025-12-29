@extends('layouts.app')
@section('title','Update Progress Tahap')

@section('content')
<div class="card">
  <div class="card-header"><h5>Update Progress - {{ $phase->nama_tahapan }}</h5></div>
  <div class="card-body">

    @if($errors->any())
      <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('project.progress.store', [$project->id, $phase->id]) }}" method="POST" enctype="multipart/form-data">
      @csrf

      <div class="form-group">
        <label>Tanggal Update</label>
        <input type="date" name="tanggal_update" class="form-control" required
               value="{{ old('tanggal_update', date('Y-m-d')) }}">
      </div>

      <div class="form-group mt-3">
        <label>Progress Tahap (%)</label>
        <input type="number" name="progress" class="form-control" min="0" max="100" required
               value="{{ old('progress', $phase->progress) }}">
        <small class="text-muted">Progress sebelumnya: {{ $phase->progress }}%</small>
      </div>

      <div class="form-group mt-3">
        <label>Catatan (opsional)</label>
        <textarea name="catatan" class="form-control">{{ old('catatan') }}</textarea>
      </div>

      <div class="form-group mt-3">
        <label>Upload Foto (maks 5 foto)</label>
        <input type="file" name="foto[]" class="form-control" multiple accept="image/png,image/jpeg">
        <small class="text-muted">Format: jpg/png. Max 2MB per foto.</small>
      </div>

      <div class="mt-4">
        <button class="btn btn-maroon">Simpan</button>
        <a href="{{ route('project.progress.index',$project->id) }}" class="btn btn-secondary">Kembali</a>
      </div>
    </form>

  </div>
</div>
@endsection
