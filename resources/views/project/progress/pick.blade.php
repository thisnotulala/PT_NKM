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
            $clientName = is_string($p->client ?? null) ? $p->client : ($p->client->nama ?? '-');
            $tanggal = $p->tanggal ?? (($p->tanggal_mulai ?? '-') . ' s/d ' . ($p->tanggal_selesai ?? '-'));

            $progress = $p->progress ?? 0;
            $status = $p->status ?? 'Aktif';

            // badge
            $badge = 'badge-warning';
            if ($status === 'Selesai') $badge = 'badge-success';
            if ($status === 'Terlambat') $badge = 'badge-danger';
            if ($status === 'Belum Mulai') $badge = 'badge-secondary';

            // ✅ normalize progress biar aman 0..100 dan numeric
            $progressNum = (float) $progress;
            if ($progressNum < 0) $progressNum = 0;
            if ($progressNum > 100) $progressNum = 100;

            // ✅ warna bar (tanpa class bootstrap)
            $barColor = '#17a2b8'; // info
            if ($progressNum >= 100) $barColor = '#28a745'; // success
            if ($status === 'Terlambat' && $progressNum < 100) $barColor = '#dc3545'; // danger
          @endphp

          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $p->nama_proyek }}</td>
            <td>{{ $clientName }}</td>
            <td>{{ $tanggal }}</td>

            {{-- ✅ Progress bar pasti tampil --}}
            <td>
              <div style="background:#e9ecef; border-radius:10px; height:18px; overflow:hidden;">
                <div
                  style="
                    width: {{ $progressNum }}%;
                    height: 18px;
                    background: {{ $barColor }};
                    line-height: 18px;
                    color: #fff;
                    font-size: 12px;
                    text-align: center;
                    min-width: {{ $progressNum > 0 ? '28px' : '0' }};
                  ">
                  {{ number_format($progressNum, 0) }}%
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
