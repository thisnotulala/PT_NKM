@extends('layouts.app')
@section('title','Update Progress Tahap')

@section('content')

{{-- Select2 CSS --}}
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
  .select2-container .select2-selection--multiple{
    min-height:38px;
    border:1px solid #ced4da;
  }
  .select2-container--default .select2-selection--multiple .select2-selection__choice{
    margin-top:6px;
  }
</style>

<div class="card">
  <div class="card-header">
    <h5>Update Progress - {{ $phase->nama_tahapan }}</h5>
  </div>

  <div class="card-body">

    {{-- ERROR --}}
    @if($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('project.progress.store', [$project->id, $phase->id]) }}"
          method="POST"
          enctype="multipart/form-data">
      @csrf

      {{-- TANGGAL --}}
      <div class="form-group">
        <label>Tanggal Update</label>
        <input type="date"
               name="tanggal_update"
               class="form-control"
               required
               value="{{ old('tanggal_update', date('Y-m-d')) }}">
      </div>

      {{-- PROGRESS --}}
      <div class="form-group mt-3">
        <label>Progress Tahap (%)</label>
        <input type="number"
               name="progress"
               class="form-control"
               min="0"
               max="100"
               required
               value="{{ old('progress', $phase->progress) }}">
        <small class="text-muted">
          Progress sebelumnya: {{ $phase->progress }}%
        </small>
      </div>

      {{-- CATATAN --}}
      <div class="form-group mt-3">
        <label>Catatan (opsional)</label>
        <textarea name="catatan"
                  class="form-control"
                  rows="3">{{ old('catatan') }}</textarea>
      </div>

      {{-- =========================
           SDM YANG BEKERJA (SEARCHABLE)
           ========================= --}}
      <hr>
      <h6>SDM yang Bekerja (hari ini)</h6>
      <small class="text-muted">
        Ketik untuk mencari nama SDM. Bisa pilih lebih dari 1.
      </small>

      <div class="form-group mt-2">
        <select name="sdm_ids[]" id="sdm_ids" class="form-control" multiple required>
          @forelse($sdms as $s)
            <option value="{{ $s->id }}"
              {{ in_array($s->id, old('sdm_ids', [])) ? 'selected' : '' }}>
              {{ $s->nama }} ({{ $s->peran }})
            </option>
          @empty
          @endforelse
        </select>

        @if($sdms->isEmpty())
          <small class="text-danger d-block mt-2">
            Belum ada data SDM untuk proyek ini.
          </small>
        @endif
      </div>

      {{-- FOTO --}}
      <div class="form-group mt-4">
        <label>Upload Foto (maks 5 foto)</label>
        <input type="file"
               name="foto[]"
               class="form-control"
               multiple
               accept="image/png,image/jpeg">
        <small class="text-muted">
          Format: JPG / PNG. Maks 2MB per foto.
        </small>
      </div>

      {{-- ACTION --}}
      <div class="mt-4">
        <button class="btn btn-maroon">
          Simpan Progress
        </button>
        <a href="{{ route('project.progress.index', $project->id) }}"
           class="btn btn-secondary">
          Kembali
        </a>
      </div>
    </form>
  </div>
<style>
  .select2-container { width:100% !important; }
  .select2-container .select2-selection--multiple{
    min-height:38px;
    border:1px solid #0080ffff;
  }
</style>
</div>
<script>
  $(document).ready(function () {
    $('#sdm_ids').select2({
      placeholder: 'Cari & pilih SDM...',
      allowClear: true,
      width: '100%'
    });
  });
</script>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    if (window.jQuery && $('#sdm_ids').length && $.fn.select2) {
      $('#sdm_ids').select2({
        placeholder: 'Cari & pilih SDM...',
        width: 'resolve'
      });
    } else {
      console.log('Select2 belum jalan: cek jQuery/select2 loaded atau dobel jQuery');
    }
  });
</script>
@endsection
