@extends('layouts.app')
@section('title','Edit Jadwal')

@section('content')
<div class="card">
  <div class="card-header"><h5>Edit Jadwal Tahap</h5></div>
  <div class="card-body">

    @if($errors->any())
      <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form action="{{ route('jadwal.update',$schedule->id) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="form-group">
        <label>Pilih Proyek</label>
        <select name="project_id" id="projectSelect" class="form-control" required>
          <option value="">-- pilih proyek --</option>
          @foreach($projects as $p)
            <option value="{{ $p->id }}" {{ old('project_id',$schedule->project_id)==$p->id?'selected':'' }}>
              {{ $p->nama_proyek }}
            </option>
          @endforeach
        </select>
      </div>

      <div class="form-group mt-3">
        <label>Pilih Tahapan</label>
        <select name="project_phase_id" id="phaseSelect" class="form-control" required>
          <option value="">Loading...</option>
        </select>
      </div>

      <div class="row mt-3">
        <div class="col-md-4">
          <label>Durasi (hari)</label>
          <input type="number" name="durasi_hari" class="form-control" min="1"
                 value="{{ old('durasi_hari', $schedule->durasi_hari) }}" required>
        </div>
        <div class="col-md-8">
          <label>Tanggal Mulai Tahap</label>
          <input type="date" name="tanggal_mulai" id="tglMulai" class="form-control" required
                 value="{{ old('tanggal_mulai', $schedule->tanggal_mulai) }}">
        </div>
      </div>

      <div class="mt-4">
        <button class="btn btn-maroon">Update</button>
        <a href="{{ route('jadwal.index') }}" class="btn btn-secondary">Kembali</a>
      </div>
    </form>
  </div>
</div>

<script>
const projectSelect = document.getElementById('projectSelect');
const phaseSelect = document.getElementById('phaseSelect');
const tglMulai = document.getElementById('tglMulai');
const currentPhaseId = {{ (int) old('project_phase_id', $schedule->project_phase_id) }};

async function loadPhases(projectId) {
  const res = await fetch(`/jadwal/phases/${projectId}`);
  const data = await res.json();

  tglMulai.min = data.project.tanggal_mulai;
  tglMulai.max = data.project.tanggal_selesai;

  phaseSelect.innerHTML = '<option value="">-- pilih tahapan --</option>';
  data.phases.forEach(p => {
    const opt = document.createElement('option');
    opt.value = p.id;
    opt.textContent = `${p.urutan}. ${p.nama_tahapan} (${p.persen}%)`;
    if (p.id === currentPhaseId) opt.selected = true;
    phaseSelect.appendChild(opt);
  });
}

projectSelect.addEventListener('change', () => {
  const pid = projectSelect.value;
  if (!pid) return;
  loadPhases(pid);
});

loadPhases(projectSelect.value);
</script>
@endsection
