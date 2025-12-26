@extends('layouts.app')
@section('title','Tambah Proyek')

@section('content')
<div class="card">
  <div class="card-header"><h5>Tambah Proyek</h5></div>

  <div class="card-body">
    @if($errors->has('tahapan_total'))
      <div class="alert alert-danger">{{ $errors->first('tahapan_total') }}</div>
    @endif

    <form action="{{ route('project.store') }}" method="POST" enctype="multipart/form-data">
      @csrf

      <div class="form-group">
        <label>Nama Proyek</label>
        <input type="text" name="nama_proyek" class="form-control" required value="{{ old('nama_proyek') }}">
        @error('nama_proyek') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      <div class="form-group mt-3">
        <label>Client</label>
        <select name="client_id" class="form-control" required>
          <option value="">-- pilih client --</option>
          @foreach($clients as $c)
            <option value="{{ $c->id }}" {{ old('client_id')==$c->id?'selected':'' }}>
              {{ $c->nama }}
            </option>
          @endforeach
        </select>
        @error('client_id') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      <div class="row mt-3">
        <div class="col-md-6">
          <label>Tanggal Mulai</label>
          <input type="date" name="tanggal_mulai" class="form-control" required value="{{ old('tanggal_mulai') }}">
          @error('tanggal_mulai') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
        <div class="col-md-6">
          <label>Tanggal Selesai</label>
          <input type="date" name="tanggal_selesai" class="form-control" required value="{{ old('tanggal_selesai') }}">
          @error('tanggal_selesai') <small class="text-danger">{{ $message }}</small> @enderror
        </div>
      </div>

      <div class="form-group mt-3">
        <label>Upload Dokumen (opsional)</label>
        <input type="file" name="dokumen" class="form-control">
        @error('dokumen') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      <hr>
      <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0">Tahapan Proyek (Total harus 100%)</h6>
        <span class="badge badge-info" id="totalBadge">Total: 0%</span>
      </div>

      <div id="tahapanWrap" class="mt-3">
        <div class="row tahapan-row">
          <div class="col-md-7">
            <label>Nama Tahapan</label>
            <input type="text" name="tahapan[0][nama_tahapan]" class="form-control" required value="{{ old('tahapan.0.nama_tahapan') }}">
          </div>
          <div class="col-md-3">
            <label>%</label>
            <input type="number" name="tahapan[0][persen]" class="form-control persen-input" min="0" max="100" value="{{ old('tahapan.0.persen', 0) }}" required>
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button type="button" class="btn btn-danger btn-block" onclick="removeRow(this)">Hapus</button>
          </div>
        </div>
      </div>

      <button type="button" class="btn btn-secondary mt-3" onclick="addTahapan()">+ Tambah Tahapan</button>

      <div class="mt-4">
        <button class="btn btn-maroon" onclick="return confirmSubmit()">Simpan</button>
        <a href="{{ route('project.index') }}" class="btn btn-secondary">Kembali</a>
      </div>
    </form>
  </div>
</div>

<script>
let idx = 1;

function calcTotal() {
  let total = 0;
  document.querySelectorAll('.persen-input').forEach(el => {
    const val = parseInt(el.value || '0', 10);
    total += isNaN(val) ? 0 : val;
  });
  document.getElementById('totalBadge').innerText = 'Total: ' + total + '%';
  return total;
}

document.addEventListener('input', function(e){
  if (e.target.classList.contains('persen-input')) calcTotal();
});

function addTahapan() {
  const wrap = document.getElementById('tahapanWrap');
  const html = `
    <div class="row tahapan-row mt-2">
      <div class="col-md-7">
        <label>Nama Tahapan</label>
        <input type="text" name="tahapan[${idx}][nama_tahapan]" class="form-control" required>
      </div>
      <div class="col-md-3">
        <label>%</label>
        <input type="number" name="tahapan[${idx}][persen]" class="form-control persen-input" min="0" max="100" value="0" required>
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button type="button" class="btn btn-danger btn-block" onclick="removeRow(this)">Hapus</button>
      </div>
    </div>
  `;
  wrap.insertAdjacentHTML('beforeend', html);
  idx++;
  calcTotal();
}

function removeRow(btn) {
  const rows = document.querySelectorAll('.tahapan-row');
  if (rows.length <= 1) return;
  btn.closest('.tahapan-row').remove();
  calcTotal();
}

function confirmSubmit() {
  const total = calcTotal();
  if (total !== 100) {
    alert('Total persentase tahapan harus 100%. Sekarang: ' + total + '%');
    return false;
  }
  return true;
}

calcTotal();
</script>
@endsection
