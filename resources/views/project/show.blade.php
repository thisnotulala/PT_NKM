@extends('layouts.app')
@section('title','Detail Proyek')

@section('content')
<div class="card">
  <div class="card-header"><h5>Detail Proyek</h5></div>
  <div class="card-body">
    <p><b>Nama:</b> {{ $project->nama_proyek }}</p>
    <p><b>Client:</b> {{ $project->client->nama ?? '-' }}</p>
    <p><b>Durasi:</b> {{ $project->tanggal_mulai }} s/d {{ $project->tanggal_selesai }}</p>

    @if($project->dokumen)
      <p><b>Dokumen:</b>
        <a target="_blank" href="{{ asset('storage/'.$project->dokumen) }}">Lihat Dokumen</a>
      </p>
    @endif

    <div class="mb-3">
    <a href="{{ route('project.jadwal.generate.form', $project->id) }}"
      class="btn btn-info">
      <i class="fas fa-magic"></i> Generate Jadwal Otomatis
    </a>

    <a href="{{ route('jadwal.index') }}"
      class="btn btn-outline-secondary">
      <i class="fas fa-calendar-alt"></i> Lihat Semua Jadwal
    </a>
  </div>

    <a href="{{ route('project.progress.index',$project->id) }}" class="btn btn-info mb-3">
      <i class="fas fa-tasks"></i> Progress Proyek
    </a>

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

    <a href="{{ route('project.index') }}" class="btn btn-secondary">Kembali</a>
  </div>
</div>
@endsection
