@extends('layouts.app')
@section('title','Progress')

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">Progress</h5>
  </div>

  <div class="card-body">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th width="50">No</th>
          <th>Nama Proyek</th>
          <th width="200">Client</th>
          <th width="220">Tanggal</th>
          <th width="180">Progress</th>
          <th width="130">Status</th>
          <th width="160">Aksi</th>
        </tr>
      </thead>

      <tbody>
        @forelse($projects as $p)
          @php
            // kalau controller sudah map object (id,nama_proyek,client,tanggal,progress,status)
            // ini aman untuk dua kondisi:
            // 1) $p->client adalah string
            // 2) $p->client adalah relasi object
            $clientName = is_string($p->client ?? null) ? $p->client : ($p->client->nama ?? '-');

            $tanggal = $p->tanggal ?? ($p->tanggal_mulai.' s/d '.$p->tanggal_selesai);

            $progress = $p->progress ?? 0;
            $status = $p->status ?? 'Aktif';

            // badge
            $badge = 'badge-warning';
            if ($status === 'Selesai') $badge = 'badge-success';
            if ($status === 'Terlambat') $badge = 'badge-danger';
            if ($status === 'Belum Mulai') $badge = 'badge-secondary';

            // bar color
            $barClass = 'bg-info';
            if ($progress >= 100) $barClass = 'bg-success';
            if ($status === 'Terlambat' && $progress < 100) $barClass = 'bg-danger';
          @endphp

          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $p->nama_proyek }}</td>
            <td>{{ $clientName }}</td>
            <td>{{ $tanggal }}</td>

            <td>
              <div class="progress" style="height: 18px;">
                <div class="progress-bar {{ $barClass }}"
                     role="progressbar"
                     style="width: {{ $progress }}%">
                  {{ $progress }}%
                </div>
              </div>
            </td>

            <td class="text-center">
              <span class="badge {{ $badge }}">{{ $status }}</span>
            </td>

            <td class="text-center">
              <a href="{{ route('project.progress.index', $p->id) }}"
                 class="btn btn-sm btn-maroon">
                Buka Progress
              </a>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-center">Belum ada proyek</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
