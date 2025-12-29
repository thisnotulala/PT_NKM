@extends('layouts.app')
@section('title','Ajukan Peminjaman')

@section('content')
<div class="card">
  <div class="card-header"><h5>Ajukan Peminjaman Equipment</h5></div>
  <div class="card-body">

    @if($errors->any())
      <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('equipment_loans.store') }}" method="POST">
      @csrf

      <div class="form-group">
        <label>Proyek</label>
        <select name="project_id" class="form-control" required>
          <option value="">-- pilih proyek --</option>
          @foreach($projects as $p)
            <option value="{{ $p->id }}" {{ old('project_id')==$p->id?'selected':'' }}>
              {{ $p->nama_proyek }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="form-group mt-3">
        <label>Tanggal Pinjam</label>
        <input type="date" name="tanggal_pinjam" class="form-control" required value="{{ old('tanggal_pinjam', date('Y-m-d')) }}">
      </div>

      <div class="form-group mt-3">
        <label>Catatan (opsional)</label>
        <textarea name="catatan" class="form-control">{{ old('catatan') }}</textarea>
      </div>

      <hr>
      <h6>Item Alat</h6>

      <div id="items">
        <div class="row item-row mt-2">
          <div class="col-md-8">
            <label>Alat</label>
            <select name="items[0][equipment_id]" class="form-control" required>
              <option value="">-- pilih alat --</option>
              @foreach($equipment as $eq)
                <option value="{{ $eq->id }}">
                  {{ $eq->nama_alat }} (stok: {{ $eq->stok }} {{ $eq->satuan->nama_satuan ?? '' }})
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-2">
            <label>Qty</label>
            <input type="number" name="items[0][qty]" class="form-control" min="1" value="1" required>
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button type="button" class="btn btn-danger btn-block" onclick="removeRow(this)">Hapus</button>
          </div>
        </div>
      </div>

      <button type="button" class="btn btn-secondary mt-3" onclick="addRow()">+ Tambah Item</button>

      <div class="mt-4">
        <button class="btn btn-maroon">Kirim Pengajuan</button>
        <a href="{{ route('equipment_loans.index') }}" class="btn btn-secondary">Kembali</a>
      </div>
    </form>
  </div>
</div>

<script>
let idx = 1;

function addRow() {
  const container = document.getElementById('items');
  const html = `
    <div class="row item-row mt-2">
      <div class="col-md-8">
        <label>Alat</label>
        <select name="items[${idx}][equipment_id]" class="form-control" required>
          <option value="">-- pilih alat --</option>
          @foreach($equipment as $eq)
            <option value="{{ $eq->id }}">{{ $eq->nama_alat }} (stok: {{ $eq->stok }} {{ $eq->satuan->nama_satuan ?? '' }})</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label>Qty</label>
        <input type="number" name="items[${idx}][qty]" class="form-control" min="1" value="1" required>
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button type="button" class="btn btn-danger btn-block" onclick="removeRow(this)">Hapus</button>
      </div>
    </div>
  `;
  container.insertAdjacentHTML('beforeend', html);
  idx++;
}

function removeRow(btn) {
  const rows = document.querySelectorAll('.item-row');
  if (rows.length <= 1) return;
  btn.closest('.item-row').remove();
}
</script>
@endsection
