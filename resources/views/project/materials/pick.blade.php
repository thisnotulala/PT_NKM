@extends('layouts.app')
@section('title','Pilih Proyek - Material')

@section('content')
<div class="card">
  <div class="card-header">
    <h5 class="mb-0">Pilih Proyek (Material Estimasi)</h5>
  </div>

  <div class="card-body">
    <table class="table table-bordered">
      <thead>
        <tr>
          <th width="60">No</th>
          <th>Nama Proyek</th>
          <th>Client</th>
          <th width="160">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($projects as $p)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $p->nama_proyek }}</td>
            <td>{{ $p->client->nama ?? '-' }}</td>
            <td class="text-center">
              <a href="{{ route('project.materials.index', $p->id) }}"
                 class="btn btn-sm btn-maroon">
                Kelola Material
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="text-center text-muted">Belum ada proyek</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
