@extends('layouts.app')
@section('title','Material Proyek')

@section('content')
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h5 class="mb-0">Material Proyek (Estimasi) - {{ $project->nama_proyek }}</h5>
    <a href="{{ route('project.progress.index',$project->id) }}" class="btn btn-secondary ml-auto">Kembali</a>
  </div>

  <div class="card-body">
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

    <form method="POST" action="{{ route('project.materials.store',$project->id) }}" class="mb-3">
      @csrf
      <div class="row">
        <div class="col-md-5">
          <label>Nama Material</label>
          <input type="text" name="nama_material" class="form-control" required>
        </div>
        <div class="col-md-2">
          <label>Satuan</label>
          <input type="text" name="satuan" class="form-control" placeholder="zak, pcs, m3">
        </div>
        <div class="col-md-3">
          <label>Qty Estimasi</label>
          <input type="number" step="0.01" name="qty_estimasi" class="form-control" required value="0">
        </div>
        <div class="col-md-2 d-flex align-items-end">
          <button class="btn btn-maroon btn-block">Tambah</button>
        </div>
      </div>
    </form>

    <table class="table table-bordered">
      <thead>
        <tr>
          <th width="50">No</th>
          <th>Material</th>
          <th width="120">Satuan</th>
          <th width="160">Estimasi</th>
          <th width="120">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($materials as $m)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $m->nama_material }}</td>
            <td class="text-center">{{ $m->satuan ?? '-' }}</td>
            <td class="text-right">{{ $m->qty_estimasi }}</td>
            <td class="text-center">
              @if(auth()->user()->role === 'site manager')
                <form method="POST" action="{{ route('project.materials.destroy', [$project->id, $m->id]) }}">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus material ini?')">Hapus</button>
                </form>
              @else
                <span class="text-muted">-</span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center text-muted">Belum ada material estimasi</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
