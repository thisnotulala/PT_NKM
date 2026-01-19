@extends('layouts.app')
@section('title','Generate Jadwal Otomatis')

@section('content')
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h5 class="mb-0">Generate Jadwal Otomatis - {{ $project->nama_proyek }}</h5>
    <a href="{{ route('project.show',$project->id) }}" class="btn btn-secondary ml-auto">Kembali</a>
  </div>

  <div class="card-body">
    @if($errors->any())
      <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="alert alert-info">
      Durasi proyek: <b>{{ $project->tanggal_mulai }}</b> s/d <b>{{ $project->tanggal_selesai }}</b>
      (Total <b>{{ $totalHari }}</b> hari)
    </div>

    <form action="{{ route('project.jadwal.generate.run',$project->id) }}" method="POST">
      @csrf

      <div class="form-group">
        <label>Mode Generate</label>
          <select name="mode" class="form-control" required>
            <option value="">-- pilih mode --</option>
            <option value="replace" {{ old('mode')=='replace'?'selected':'' }}>Replace (hapus jadwal lama, buat ulang)</option>
            <option value="skip" {{ old('mode')=='skip'?'selected':'' }}>Skip (yang sudah ada tidak diubah)</option>
          </select>
          @error('mode') <small class="text-danger">{{ $message }}</small> @enderror

      </div>

      <hr>
      <h6>Durasi per Tahap (hari)</h6>

      <table class="table table-bordered">
        <thead>
          <tr>
            <th width="60">Urut</th>
            <th>Tahapan</th>
            <th width="120">Persen</th>
            <th width="140">Durasi (hari)</th>
          </tr>
        </thead>
        <tbody>
          @foreach($phases as $p)
          <tr>
            <td class="text-center">{{ $p['urutan'] }}</td>
            <td>{{ $p['nama_tahapan'] }}</td>
            <td class="text-center">{{ $p['persen'] }}%</td>
            <td>
              <input type="number"
                class="form-control durasi-input"
                name="durasi[{{ $p['id'] }}]"
                min="1"
                value="{{ old('durasi.'.$p['id'], $p['durasi_default']) }}"
              >

            </td>
          </tr>
          @endforeach
        </tbody>
      </table>

      <div class="d-flex justify-content-between align-items-center">
        <span class="badge badge-info" id="totalDurasiBadge">Total Durasi: 0 hari</span>
        <button class="btn btn-maroon" onclick="return confirmSubmit()">Generate & Simpan</button>
      </div>
    </form>
  </div>
</div>

<script>
const inputs = document.querySelectorAll('.durasi-input');
const badge = document.getElementById('totalDurasiBadge');
const totalHari = {{ (int)$totalHari }};

function calc() {
  let sum = 0;
  inputs.forEach(i => sum += parseInt(i.value || '0', 10));
  badge.innerText = 'Total Durasi: ' + sum + ' hari (maks ' + totalHari + ')';
  return sum;
}
inputs.forEach(i => i.addEventListener('input', calc));

function confirmSubmit(){
  const sum = calc();
  if (sum > totalHari) {
    alert('Total durasi melebihi durasi proyek!');
    return false;
  }
  return confirm('Generate jadwal otomatis?');
}
calc();
</script>
@endsection
