@extends('layouts.app')
@section('title','Data Proyek')

@section('content')
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h5 class="mb-0">Data Proyek</h5>
    <a href="{{ route('project.create') }}" class="btn btn-maroon ml-auto">
      <i class="fas fa-plus"></i> Tambah Proyek
    </a>
  </div>

  <div class="card-body">
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th width="50">No</th>
          <th>Nama Proyek</th>
          <th>Client</th>
          <th>Durasi</th>
          <th width="200">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($projects as $p)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $p->nama_proyek }}</td>
          <td>{{ $p->client->nama ?? '-' }}</td>
          <td>{{ $p->tanggal_mulai }} s/d {{ $p->tanggal_selesai }}</td>
          <td>
            <div class="d-flex" style="gap:8px; flex-wrap:wrap;">
              <a href="{{ route('project.show',$p->id) }}" class="btn btn-sm btn-secondary">Detail</a>
              <a href="{{ route('project.edit',$p->id) }}" class="btn btn-sm btn-warning">Edit</a>
              <form action="{{ route('project.destroy',$p->id) }}" method="POST">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus proyek ini?')">Hapus</button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center">Data proyek kosong</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
