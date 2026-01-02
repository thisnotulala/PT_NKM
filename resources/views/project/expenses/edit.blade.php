@extends('layouts.app')
@section('title','Edit Pengeluaran')

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">Edit Pengeluaran - {{ $project->nama_proyek }}</h5>
  </div>

  <div class="card-body">
    @if($errors->any())
      <div class="alert alert-danger">
        {{ $errors->first() }}
      </div>
    @endif

    <form action="{{ route('project.expenses.update', [$project->id, $expense->id]) }}" method="POST" enctype="multipart/form-data" autocomplete="off">
      @csrf
      @method('PUT')

      <div class="form-group">
        <label>Tanggal</label>
        <input type="date" name="tanggal" class="form-control" required
               value="{{ old('tanggal', $expense->tanggal) }}">
        <small class="text-muted">Rentang proyek: {{ $project->tanggal_mulai }} s/d {{ $project->tanggal_selesai }}</small>
      </div>

      <div class="form-group mt-3">
        <label>Kategori</label>
        <select name="kategori" class="form-control" required>
          @php $kats = ['Material','SDM','Equipment','Operasional','Lainnya']; @endphp
          <option value="">-- pilih kategori --</option>
          @foreach($kats as $k)
            <option value="{{ $k }}" {{ old('kategori', $expense->kategori) == $k ? 'selected' : '' }}>
              {{ $k }}
            </option>
          @endforeach
        </select>
      </div>

      {{-- MATERIAL FIELDS --}}
      <div id="materialFields" class="mt-3" style="display:none;">
        <div class="form-group">
          <label>Qty (Material)</label>
          <input type="number" step="0.01" name="qty" class="form-control"
                 value="{{ old('qty', $expense->qty) }}" placeholder="contoh: 10">
        </div>

        <div class="form-group mt-3">
          <label>Satuan</label>
          <select name="satuan_id" class="form-control">
            <option value="">-- pilih satuan --</option>
            @foreach($satuans as $st)
              <option value="{{ $st->id }}" {{ old('satuan_id', $expense->satuan_id) == $st->id ? 'selected' : '' }}>
                {{ $st->nama_satuan ?? $st->nama ?? '-' }}
              </option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="form-group mt-3">
        <label>Nominal</label>
        <input type="number" name="nominal" class="form-control" required min="1"
               value="{{ old('nominal', $expense->nominal) }}">
      </div>

      <div class="form-group mt-3">
        <label>Keterangan (opsional)</label>
        <input type="text" name="keterangan" class="form-control"
               value="{{ old('keterangan', $expense->keterangan) }}">
      </div>

      <div class="form-group mt-3">
        <label>SDM (opsional)</label>
        <select name="sdm_id" class="form-control">
          <option value="">-- tidak ada --</option>
          @foreach($sdms as $s)
            <option value="{{ $s->id }}" {{ old('sdm_id', $expense->sdm_id) == $s->id ? 'selected' : '' }}>
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
            <option value="{{ $eq->id }}" {{ old('equipment_id', $expense->equipment_id) == $eq->id ? 'selected' : '' }}>
              {{ $eq->nama_alat }} (Stok: {{ $eq->stok }})
            </option>
          @endforeach
        </select>
      </div>

      <div class="form-group mt-3">
        <label>Upload Bukti (opsional)</label>
        <input type="file" name="bukti" class="form-control" accept="image/png,image/jpeg,application/pdf">
        <small class="text-muted">Format: jpg/png/pdf. Maks 4MB.</small>

        @if($expense->bukti_path)
          <div class="mt-2">
            Bukti saat ini:
            <a target="_blank" href="{{ asset('storage/'.$expense->bukti_path) }}">Lihat</a>
          </div>
        @endif
      </div>

      <div class="mt-4">
        <button class="btn btn-maroon">Update</button>
        <a href="{{ route('project.expenses.index', $project->id) }}" class="btn btn-secondary">Kembali</a>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
  const kategori = document.querySelector('select[name="kategori"]');
  const materialBox = document.getElementById('materialFields');

  function toggleMaterial() {
    if (!kategori || !materialBox) return;
    if (kategori.value === 'Material') {
      materialBox.style.display = 'block';
    } else {
      materialBox.style.display = 'none';
    }
  }

  if (kategori) {
    kategori.addEventListener('change', toggleMaterial);
    toggleMaterial();
  }
});
</script>
@endsection
