@extends('layouts.app')
@section('title','Detail Proyek')

@section('content')
<div class="card">

  {{-- ✅ HEADER TANPA TOMBOL PENGELUARAN --}}
  <div class="card-header">
    <h5 class="mb-0">Detail Proyek</h5>
  </div>

  <div class="card-body">
    <p><b>Nama:</b> {{ $project->nama_proyek }}</p>
    <p><b>Client:</b> {{ $project->client->nama ?? '-' }}</p>
    <p><b>Durasi:</b> {{ $project->tanggal_mulai }} s/d {{ $project->tanggal_selesai }}</p>

    @if($project->rab_path)
      <p>
        <b>RAB:</b>
        <a href="{{ asset('storage/'.$project->rab_path) }}" target="_blank">
          Lihat / Download RAB
        </a>
      </p>
    @else
      <p><b>RAB:</b> <span class="text-muted">Belum diupload</span></p>
    @endif

    @if($project->dokumen)
      <p><b>Dokumen:</b>
        <a target="_blank" href="{{ asset('storage/'.$project->dokumen) }}">Lihat Dokumen</a>
      </p>
    @endif

    <div class="mb-3">
      <a href="{{ route('project.jadwal.generate.form', $project->id) }}" class="btn btn-info">
        <i class="fas fa-magic"></i> Generate Jadwal Otomatis
      </a>

      <a href="{{ route('jadwal.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-calendar-alt"></i> Lihat Semua Jadwal
      </a>
    </div>

    <hr>
    <h6>Tahapan Proyek</h6>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th width="50">No</th>
          <th>Nama Tahapan</th>
          <th width="100">%</th>
        </tr>
      </thead>
      <tbody>
        @php $total = 0; @endphp
        @foreach($project->phases->sortBy('urutan') as $ph)
          @php $total += $ph->persen; @endphp
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $ph->nama_tahapan }}</td>
            <td class="text-center">{{ $ph->persen }}</td>
          </tr>
        @endforeach
        <tr>
          <td colspan="2" class="text-right"><b>Total</b></td>
          <td class="text-center"><b>{{ $total }}</b></td>
        </tr>
      </tbody>
    </table>

    <hr>
    <h6>Tim SDM</h6>

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card mb-3">
      <div class="card-body">

        {{-- FORM TAMBAH SDM KE PROYEK --}}
        <form action="{{ route('project.sdm.store', $project->id) }}" method="POST" class="mb-3">
          @csrf
          <div class="row">
            <div class="col-md-5">
              <label>SDM</label>
              <select name="sdm_id" class="form-control" required>
                <option value="">-- pilih SDM --</option>
                @foreach($sdms as $s)
                  <option value="{{ $s->id }}">
                    {{ $s->nama }} - {{ $s->peran }}
                  </option>
                @endforeach
              </select>
            </div>

            <div class="col-md-5">
              <label>Peran di Proyek (opsional)</label>
              <input type="text" name="peran_di_proyek" class="form-control" placeholder="misal: PIC, Supervisor">
            </div>

            <div class="col-md-2 d-flex align-items-end">
              <button class="btn btn-maroon btn-block">Tambah</button>
            </div>
          </div>
        </form>

        {{-- LIST SDM YANG SUDAH DITUGASKAN --}}
        <table class="table table-bordered">
          <thead>
            <tr>
              <th width="50">No</th>
              <th>Nama</th>
              <th>Peran (Master)</th>
              <th>Peran di Proyek</th>
              <th width="140">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse($project->projectSdms as $as)
            <tr>
              <td>{{ $loop->iteration }}</td>
              <td>{{ $as->sdm->nama ?? '-' }}</td>
              <td>{{ $as->sdm->peran ?? '-' }}</td>
              <td>{{ $as->peran_di_proyek ?? '-' }}</td>
              <td>
                <form action="{{ route('project.sdm.destroy', [$project->id, $as->id]) }}" method="POST">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-danger btn-sm"
                          onclick="return confirm('Hapus SDM dari proyek ini?')">
                    Hapus
                  </button>
                </form>
              </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center">Belum ada SDM ditugaskan</td></tr>
            @endforelse
          </tbody>
        </table>

      </div>
    </div>

    <a href="{{ route('project.index') }}" class="btn btn-secondary">Kembali</a>
  </div>
</div>
@endsection
