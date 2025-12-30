@extends('layouts.app')
@section('title','Laporan Proyek')

@section('content')
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h5 class="mb-0">Laporan Proyek</h5>
  </div>

  <div class="card-body">
    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th width="50">No</th>
          <th>Nama Proyek</th>
          <th width="200">Client</th>
          <th width="220">Tanggal</th>
          <th width="180">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($projects as $p)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $p->nama_proyek }}</td>
          <td>{{ $p->client->nama ?? '-' }}</td>
          <td>{{ $p->tanggal_mulai }} s/d {{ $p->tanggal_selesai }}</td>
          <td class="text-center">
            <a href="{{ route('report.project.pdf', $p->id) }}" class="btn btn-sm btn-maroon">
              <i class="fas fa-print"></i> Cetak PDF
            </a>
          </td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center">Belum ada proyek</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
