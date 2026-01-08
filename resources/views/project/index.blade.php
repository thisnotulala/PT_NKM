@extends('layouts.app')
@section('title','Data Proyek')

@section('content')
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h5 class="mb-0">Data Proyek</h5>

    {{-- Tambah Proyek: hanya site manager & administrasi --}}
    @if(in_array(auth()->user()->role, ['site manager','administrasi']))
      <a href="{{ route('project.create') }}" class="btn btn-maroon ml-auto">
        <i class="fas fa-plus"></i> Tambah Proyek
      </a>
    @endif
  </div>

  <div class="card-body">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

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

              {{-- Detail: semua role --}}
              <a href="{{ route('project.show',$p->id) }}" class="btn btn-sm btn-secondary">
                Detail
              </a>

              {{-- Edit: hanya site manager & administrasi --}}
              @if(in_array(auth()->user()->role, ['site manager','administrasi']))
                <a href="{{ route('project.edit',$p->id) }}" class="btn btn-sm btn-warning">
                  Edit
                </a>
              @endif

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