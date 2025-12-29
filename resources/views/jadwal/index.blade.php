@extends('layouts.app')
@section('title','Jadwal Tahapan')

@section('content')
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h5 class="mb-0">Jadwal Tahapan Proyek</h5>
    <a href="{{ route('jadwal.create') }}" class="btn btn-maroon ml-auto">
      <i class="fas fa-plus"></i> Tambah Jadwal
    </a>
  </div>

  <div class="card-body">
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th width="50">No</th>
          <th>Proyek</th>
          <th>Tahap</th>
          <th width="140">Mulai</th>
          <th width="140">Selesai</th>
          <th width="110">Durasi</th>
          <th width="140">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($schedules as $s)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $s->project->nama_proyek }}</td>
          <td>{{ $s->phase->nama_tahapan }} ({{ $s->phase->persen }}%)</td>
          <td>{{ $s->tanggal_mulai }}</td>
          <td>{{ $s->tanggal_selesai }}</td>
          <td class="text-center">{{ $s->durasi_hari }} hari</td>
          <td>
            <div class="action-group">
              <a href="{{ route('jadwal.edit',$s->id) }}" class="btn-action btn-edit" title="Edit">
                <i class="fas fa-pen"></i>
              </a>
              <form action="{{ route('jadwal.destroy',$s->id) }}" method="POST">
                @csrf @method('DELETE')
                <button class="btn-action btn-delete" onclick="return confirm('Hapus jadwal ini?')" title="Hapus">
                  <i class="fas fa-trash"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center">Belum ada jadwal</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
