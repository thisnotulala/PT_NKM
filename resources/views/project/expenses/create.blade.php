@extends('layouts.app')
@section('title','Tambah Pengeluaran')

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">Tambah Pengeluaran - {{ $project->nama_proyek }}</h5>
  </div>

  <div class="card-body">
    @if($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('project.expenses.store', $project->id) }}"
          method="POST"
          enctype="multipart/form-data"
          autocomplete="off">
      @csrf

      <div class="form-group">
        <label>Tanggal</label>
        <input type="date" name="tanggal" class="form-control" required
               value="{{ old('tanggal', date('Y-m-d')) }}">
        <small class="text-muted">
          Rentang proyek: {{ $project->tanggal_mulai }} s/d {{ $project->tanggal_selesai }}
        </small>
      </div>

      <div class="form-group mt-3">
        <label>Kategori</label>
        <select name="kategori" class="form-control" required>
          @php $kats = ['Material','SDM','Equipment','Operasional','Lainnya']; @endphp
          <option value="">-- pilih kategori --</option>
          @foreach($kats as $k)
            <option value="{{ $k }}" {{ old('kategori') == $k ? 'selected' : '' }}>
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
                 value="{{ old('qty') }}" placeholder="contoh: 10">
          <small class="text-muted">Wajib diisi jika kategori Material.</small>
        </div>

        <div class="form-group mt-3">
          <label>Satuan</label>
          <select name="satuan_id" class="form-control">
            <option value="">-- pilih satuan --</option>
            @foreach($satuans as $st)
              <option value="{{ $st->id }}" {{ old('satuan_id') == $st->id ? 'selected' : '' }}>
                {{ $st->nama_satuan ?? $st->nama ?? '-' }}
              </option>
            @endforeach
          </select>
          <small class="text-muted">Wajib diisi jika kategori Material.</small>
        </div>
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
        <label>Upload Bukti <span class="text-danger">*</span></label>
        <input type="file"
               name="bukti"
               class="form-control"
               required
               accept="image/png,image/jpeg,application/pdf">
        <small class="text-muted">Wajib diisi. Format: jpg/png/pdf. Maks 4MB.</small>
      </div>

      <div class="mt-4">
        <button type="submit" class="btn btn-maroon">Simpan</button>
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

      // kosongkan jika pindah kategori
      const qty = materialBox.querySelector('input[name="qty"]');
      const satuan = materialBox.querySelector('select[name="satuan_id"]');
      if (qty) qty.value = '';
      if (satuan) satuan.value = '';
    }
  }

  kategori.addEventListener('change', toggleMaterial);
  toggleMaterial(); // saat load (handle old('kategori'))
});
</script>
@endsection
