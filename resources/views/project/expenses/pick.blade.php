@extends('layouts.app')
@section('title','Pengeluaran')

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">Pengeluaran</h5>
  </div>

  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-bordered table-hover mb-0">
        <thead>
          <tr>
            <th width="50">No</th>
            <th>Nama Proyek</th>
            <th width="200">Client</th>
            <th width="220">Tanggal</th>
            <th width="200">Total Pengeluaran</th>
            <th width="160">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($projects as $p)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $p->nama_proyek }}</td>
            <td>{{ $p->client->nama ?? '-' }}</td>
            <td>{{ $p->tanggal_mulai }} s/d {{ $p->tanggal_selesai }}</td>

            <td class="text-right">
              Rp {{ number_format($p->expenses_sum_nominal ?? 0, 0, ',', '.') }}
            </td>

            <td class="text-center">
              <a href="{{ route('project.expenses.index', $p->id) }}" class="btn btn-sm btn-maroon">
                Buka Pengeluaran
              </a>
            </td>
          </tr>
          @empty
          <tr><td colspan="6" class="text-center">Belum ada proyek</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection
