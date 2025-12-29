@extends('layouts.app')
@section('title','Progress Proyek')

@section('content')
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h5 class="mb-0">Progress - {{ $project->nama_proyek }}</h5>
    <a href="{{ route('project.show',$project->id) }}" class="btn btn-secondary ml-auto">Detail Proyek</a>
  </div>

  <div class="card-body">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="mb-3">
      <b>Progress Total:</b> {{ number_format($progressTotal,1) }}%
      <div class="progress mt-2" style="height: 18px;">
        <div class="progress-bar" role="progressbar" style="width: {{ $progressTotal }}%">
          {{ number_format($progressTotal,0) }}%
        </div>
      </div>
    </div>

    <hr>
    <h6>Daftar Tahapan</h6>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th width="50">No</th>
          <th>Tahapan</th>
          <th width="90">Bobot</th>
          <th width="120">Progress</th>
          <th width="160">Update</th>
        </tr>
      </thead>
      <tbody>
        @foreach($project->phases as $ph)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $ph->nama_tahapan }}</td>
          <td class="text-center">{{ $ph->persen }}%</td>
          <td class="text-center">
            {{ $ph->progress }}%
            @if($ph->last_progress_at)
              <br><small class="text-muted">{{ $ph->last_progress_at }}</small>
            @endif
          </td>
          <td class="text-center">

            @if($ph->progress >= 100)
              <button class="btn btn-sm btn-secondary" disabled>
                Selesai
              </button>
            @else
              <a href="{{ route('project.progress.create', [$project->id, $ph->id]) }}"
                 class="btn btn-sm btn-maroon">
                Update Progress
              </a>
            @endif

          </td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <hr>
    <h6>Riwayat Progress (Log)</h6>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th width="140">Tanggal</th>
          <th>Tahapan</th>
          <th width="100">Progress</th>
          <th>Catatan</th>
          <th width="200">Foto</th>
        </tr>
      </thead>
      <tbody>
        @forelse($logs as $l)
        <tr>
          <td>{{ $l->tanggal_update }}</td>
          <td>{{ $l->phase->nama_tahapan }}</td>
          <td class="text-center">{{ $l->progress }}%</td>
          <td>{{ $l->catatan }}</td>
          <td>
            @forelse($l->photos as $p)
              <a href="{{ asset('storage/'.$p->photo_path) }}" target="_blank">Lihat</a><br>
            @empty
              -
            @endforelse
          </td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center">Belum ada progress</td></tr>
        @endforelse
      </tbody>
    </table>

  </div>
</div>
@endsection
