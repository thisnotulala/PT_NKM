@extends('layouts.app')
@section('title','Pengeluaran Proyek')

@section('content')
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h5 class="mb-0">Pengeluaran - {{ $project->nama_proyek }}</h5>

    <a href="{{ route('project.show', $project->id) }}" class="btn btn-secondary ml-auto">
      Detail Proyek
    </a>

    <a href="{{ route('project.expenses.create', $project->id) }}" class="btn btn-maroon ml-2">
      <i class="fas fa-plus"></i> Tambah Pengeluaran
    </a>
  </div>

  <div class="card-body">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="mb-3">
      <b>Total Pengeluaran:</b> Rp {{ number_format($total, 0, ',', '.') }}
    </div>

    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th width="50">No</th>
          <th width="120">Tanggal</th>
          <th width="140">Kategori</th>
          <th>Keterangan</th>
          <th width="140">SDM</th>
          <th width="160">Equipment</th>
          <th width="160" class="text-right">Nominal</th>
          <th width="110">Bukti</th>
          <th width="140">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($expenses as $e)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $e->tanggal }}</td>
          <td>{{ $e->kategori }}</td>
          <td>{{ $e->keterangan ?? '-' }}</td>
          <td>{{ $e->sdm->nama ?? '-' }}</td>
          <td>{{ $e->equipment->nama_alat ?? '-' }}</td>
          <td class="text-right">Rp {{ number_format($e->nominal, 0, ',', '.') }}</td>
          <td class="text-center">
            @if($e->bukti_path)
              <a target="_blank" href="{{ asset('storage/'.$e->bukti_path) }}">Lihat</a>
            @else
              -
            @endif
          </td>
          <td>
            <div class="action-group">
              <a href="{{ route('project.expenses.edit', [$project->id, $e->id]) }}"
                 class="btn-action btn-edit" title="Edit">
                <i class="fas fa-pen"></i>
              </a>

              <form action="{{ route('project.expenses.destroy', [$project->id, $e->id]) }}" method="POST">
                @csrf
                @method('DELETE')
                <button class="btn-action btn-delete"
                        onclick="return confirm('Hapus pengeluaran ini?')" title="Hapus">
                  <i class="fas fa-trash"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="9" class="text-center">Belum ada pengeluaran</td></tr>
        @endforelse
      </tbody>
    </table>

  </div>
</div>
@endsection
