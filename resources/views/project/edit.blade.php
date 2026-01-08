@extends('layouts.app')
@section('title','Edit Proyek')

@section('content')
<div class="card">
  <div class="card-header"><h5>Edit Proyek</h5></div>

  <div class="card-body">
    @if($errors->has('tahapan_total'))
      <div class="alert alert-danger">{{ $errors->first('tahapan_total') }}</div>
    @endif

    <form action="{{ route('project.update',$project->id) }}" method="POST" enctype="multipart/form-data">
      @csrf
      @method('PUT')

      <div class="form-group">
        <label>Nama Proyek</label>
        <input type="text" name="nama_proyek" class="form-control" required
               value="{{ old('nama_proyek', $project->nama_proyek) }}">
      </div>

      <div class="form-group mt-3">
        <label>Client</label>
        <select name="client_id" class="form-control" required>
          @foreach($clients as $c)
            <option value="{{ $c->id }}" {{ old('client_id', $project->client_id)==$c->id?'selected':'' }}>
              {{ $c->nama }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="row mt-3">
        <div class="col-md-6">
          <label>Tanggal Mulai</label>
          <input type="date" name="tanggal_mulai" class="form-control" required
                 value="{{ old('tanggal_mulai', $project->tanggal_mulai) }}">
        </div>
        <div class="col-md-6">
          <label>Tanggal Selesai</label>
          <input type="date" name="tanggal_selesai" class="form-control" required
                 value="{{ old('tanggal_selesai', $project->tanggal_selesai) }}">
        </div>
      </div>

      <div class="form-group mt-3">
        <label>Upload Dokumen (opsional)</label>
        <input type="file" name="dokumen" class="form-control">
        @if($project->dokumen)
          <small class="text-muted d-block mt-1">
            Dokumen saat ini: <a target="_blank" href="{{ asset('storage/'.$project->dokumen) }}">Lihat</a>
          </small>
        @endif
      </div>
      
      <div class="form-group mt-3">
        <label>Upload RAB (PDF / Excel)</label>
        <input type="file" name="rab" class="form-control"
              accept="application/pdf,application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet">

        @if($project->rab_path)
          <small class="text-muted d-block mt-1">
            RAB saat ini:
            <a href="{{ asset('storage/'.$project->rab_path) }}" target="_blank">Lihat</a>
          </small>
        @endif
      </div>


      <hr>
      <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0">Tahapan Proyek (Total harus 100%)</h6>
        <span class="badge badge-info" id="totalBadge">Total: 0%</span>
      </div>

      <div id="tahapanWrap" class="mt-3">
        @php
          $phases = old('tahapan') ?? $project->phases->sortBy('urutan')->values()->map(fn($p)=>[
            'nama_tahapan'=>$p->nama_tahapan,'persen'=>$p->persen
          ])->toArray();
        @endphp

        @foreach($phases as $i => $t)
        <div class="row tahapan-row {{ $i>0 ? 'mt-2':'' }}">
          <div class="col-md-7">
            <label>Nama Tahapan</label>
            <input type="text" name="tahapan[{{ $i }}][nama_tahapan]" class="form-control" required value="{{ $t['nama_tahapan'] }}">
          </div>
          <div class="col-md-3">
            <label>%</label>
            <input type="number" name="tahapan[{{ $i }}][persen]" class="form-control persen-input" min="0" max="100" required value="{{ $t['persen'] }}">
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button type="button" class="btn btn-danger btn-block" onclick="removeRow(this)">Hapus</button>
          </div>
        </div>
        @endforeach
      </div>

      <button type="button" class="btn btn-secondary mt-3" onclick="addTahapan()">+ Tambah Tahapan</button>

      <div class="mt-4">
        <button class="btn btn-maroon" onclick="return confirmSubmit()">Update</button>
        <a href="{{ route('project.index') }}" class="btn btn-secondary">Kembali</a>
      </div>
    </form>
  </div>
</div>

<script>
let idx = document.querySelectorAll('.tahapan-row').length;

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