@extends('layouts.app')

@section('title','Update Progress Tahapan')

@section('content')
<div class="card">
  <div class="card-header d-flex align-items-center justify-content-between">
    <h5 class="mb-0">Update Progress Tahapan</h5>
    <a href="{{ route('project.progress.index', $project->id) }}" class="btn btn-secondary btn-sm">Kembali</a>
  </div>

  <div class="card-body">

    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @php
      $current = (int) ($phase->progress ?? 0);
      $remaining = max(0, 100 - $current);
    @endphp

    <div class="alert alert-info">
      <div><b>Proyek:</b> {{ $project->nama_proyek ?? '-' }}</div>
      <div><b>Tahapan:</b> {{ $phase->nama_tahapan ?? '-' }}</div>
      <div><b>Progress saat ini:</b> <span id="currentProgress">{{ $current }}</span>%</div>
      <div><b>Maksimal yang bisa ditambahkan:</b> <span id="remainingProgress">{{ $remaining }}</span>%</div>
    </div>

    <div id="progressAlert" class="alert alert-danger d-none"></div>

    <form action="{{ route('project.progress.store', [$project->id, $phase->id]) }}"
          method="POST"
          enctype="multipart/form-data"
          novalidate>
      @csrf

      <div class="form-group">
        <label>Tanggal Update</label>
        <input type="date" name="tanggal_update" class="form-control" required value="{{ old('tanggal_update', now()->toDateString()) }}">
        @error('tanggal_update') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      <div class="form-group mt-3">
        <label>Tambah Progress (%)</label>
        <input type="number"
               id="progressInput"
               name="progress"
               class="form-control"
               min="1"
               max="{{ $remaining }}"
               required
               value="{{ old('progress') }}">
        <small class="text-muted">
          Isi progress sebagai <b>tambahan</b> (delta). Max otomatis = sisa sampai 100%.
        </small>
        @error('progress') <small class="text-danger d-block">{{ $message }}</small> @enderror
      </div>

      <div class="form-group mt-3">
        <label>Catatan (opsional)</label>
        <textarea name="catatan" class="form-control" rows="3">{{ old('catatan') }}</textarea>
        @error('catatan') <small class="text-danger">{{ $message }}</small> @enderror
      </div>

      <hr>

      <div class="form-group">
        <label>Pilih SDM (opsional)</label>
        <select name="sdm_ids[]" class="form-control" multiple>
          @foreach($sdms as $s)
            <option value="{{ $s->id }}"
              {{ (collect(old('sdm_ids', []))->contains($s->id)) ? 'selected' : '' }}>
              {{ $s->nama }}
            </option>
          @endforeach
        </select>
        <small class="text-muted">Boleh pilih lebih dari 1.</small>
        @error('sdm_ids') <small class="text-danger d-block">{{ $message }}</small> @enderror
        @error('sdm_ids.*') <small class="text-danger d-block">{{ $message }}</small> @enderror
      </div>

      <hr>

      <div class="d-flex align-items-center justify-content-between">
        <h6 class="mb-0">Pemakaian Material (opsional)</h6>
        <button type="button" class="btn btn-secondary btn-sm" onclick="addMaterialRow()">+ Tambah Baris</button>
      </div>

      <div id="materialsWrap" class="mt-3">
        @php
          $oldMaterials = old('materials', []);
          if (empty($oldMaterials)) $oldMaterials = [[]];
        @endphp

        @foreach($oldMaterials as $i => $row)
          <div class="row material-row mb-2">
            <div class="col-md-7">
              <label>Material</label>
              <select name="materials[{{ $i }}][project_material_id]" class="form-control">
                <option value="">-- pilih material --</option>
                @foreach($projectMaterials as $pm)
                  <option value="{{ $pm->id }}"
                    {{ (string)($row['project_material_id'] ?? '') === (string)$pm->id ? 'selected' : '' }}>
                    {{ $pm->nama_material }}
                  </option>
                @endforeach
              </select>
            </div>
            <div class="col-md-3">
              <label>Qty Pakai</label>
              <input type="number" step="0.01" min="0"
                     name="materials[{{ $i }}][qty_pakai]"
                     class="form-control"
                     value="{{ $row['qty_pakai'] ?? '' }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
              <button type="button" class="btn btn-danger btn-block" onclick="removeMaterialRow(this)">Hapus</button>
            </div>
          </div>
        @endforeach
      </div>

      @error('materials') <small class="text-danger d-block">{{ $message }}</small> @enderror
      @error('materials.*.project_material_id') <small class="text-danger d-block">{{ $message }}</small> @enderror
      @error('materials.*.qty_pakai') <small class="text-danger d-block">{{ $message }}</small> @enderror

      <hr>

      <div class="form-group">
        <label>Upload Foto Progress (opsional, bisa lebih dari 1)</label>
        <input type="file" name="foto[]" class="form-control" multiple accept="image/png,image/jpeg,image/jpg">
        @error('foto') <small class="text-danger d-block">{{ $message }}</small> @enderror
        @error('foto.*') <small class="text-danger d-block">{{ $message }}</small> @enderror
      </div>

      <div class="mt-4 d-flex gap-2">
        <button type="submit" id="submitBtn" class="btn btn-maroon">Simpan</button>
        <a href="{{ route('project.progress.index', $project->id) }}" class="btn btn-secondary">Batal</a>
      </div>
    </form>
  </div>
</div>

<script>
let materialIdx = document.querySelectorAll('#materialsWrap .material-row').length;

function addMaterialRow() {
  const wrap = document.getElementById('materialsWrap');
  const html = `
    <div class="row material-row mb-2">
      <div class="col-md-7">
        <label>Material</label>
        <select name="materials[${materialIdx}][project_material_id]" class="form-control">
          <option value="">-- pilih material --</option>
          @foreach($projectMaterials as $pm)
            <option value="{{ $pm->id }}">{{ $pm->nama_material }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label>Qty Pakai</label>
        <input type="number" step="0.01" min="0" name="materials[${materialIdx}][qty_pakai]" class="form-control" value="">
      </div>
      <div class="col-md-2 d-flex align-items-end">
        <button type="button" class="btn btn-danger btn-block" onclick="removeMaterialRow(this)">Hapus</button>
      </div>
    </div>
  `;
  wrap.insertAdjacentHTML('beforeend', html);
  materialIdx++;
}

function removeMaterialRow(btn) {
  const rows = document.querySelectorAll('#materialsWrap .material-row');
  if (rows.length <= 1) return;
  btn.closest('.material-row').remove();
}

function validateProgress() {
  const current = parseInt(document.getElementById('currentProgress').innerText || '0', 10);
  const remaining = parseInt(document.getElementById('remainingProgress').innerText || '0', 10);

  const input = document.getElementById('progressInput');
  const alertBox = document.getElementById('progressAlert');
  const submitBtn = document.getElementById('submitBtn');

  const val = parseInt(input.value || '0', 10);

  alertBox.classList.add('d-none');
  submitBtn.disabled = false;

  if (remaining <= 0) {
    alertBox.classList.remove('d-none');
    alertBox.innerText = 'Tahapan ini sudah 100%, tidak bisa diupdate.';
    submitBtn.disabled = true;
    return false;
  }

  if (isNaN(val) || val < 1) return true;

  if (val > remaining) {
    alertBox.classList.remove('d-none');
    alertBox.innerText = 'Maksimal progress yang bisa ditambahkan: ' + remaining + '%. (Progress saat ini ' + current + '%)';
    submitBtn.disabled = true;
    return false;
  }

  return true;
}

document.addEventListener('input', function(e){
  if (e.target && e.target.id === 'progressInput') validateProgress();
});

validateProgress();
</script>
@endsection
