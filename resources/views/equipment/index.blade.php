@extends('layouts.app')
@section('title', 'Data Equipment')

@section('content')
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h5 class="mb-0">Data Equipment</h5>
    <a href="{{ route('equipment.create') }}" class="btn btn-maroon ml-auto">
      <i class="fas fa-plus"></i> Tambah Alat
    </a>
  </div>

  <div class="card-body">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th width="50">No</th>
          <th>Nama Alat</th>
          <th width="120">Satuan</th>
          <th width="90">Stok</th>
          <th width="140">Kondisi</th>
          <th width="140">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($equipment as $eq)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $eq->nama_alat }}</td>
          <td>{{ $eq->satuan->nama_satuan ?? '-' }}</td>
          <td class="text-center">{{ $eq->stok }}</td>
          <td>{{ $eq->kondisi }}</td>
          <td>
            <div class="action-group">
              <a href="{{ route('equipment.edit', $eq->id) }}" class="btn-action btn-edit" title="Edit">
                <i class="fas fa-pen"></i>
              </a>
              <form action="{{ route('equipment.destroy', $eq->id) }}" method="POST">
                @csrf @method('DELETE')
                <button onclick="return confirm('Hapus alat ini?')"
                        class="btn-action btn-delete" title="Hapus">
                  <i class="fas fa-trash"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center">Data equipment kosong</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
