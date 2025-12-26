@extends('layouts.app')
@section('title','SDM Proyek')

@section('content')
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h5 class="mb-0">Atur SDM - {{ $project->nama_proyek }}</h5>
    <a href="{{ route('project.show',$project->id) }}" class="btn btn-secondary ml-auto">Kembali</a>
  </div>

  <div class="card-body">
    <form action="{{ route('project.sdm.update',$project->id) }}" method="POST">
      @csrf
      @method('PUT')

      <div class="alert alert-info mb-3">
        Pilih SDM yang terlibat pada proyek ini.
      </div>

      <div class="table-responsive">
        <table class="table table-bordered table-hover">
          <thead>
            <tr>
              <th width="60">Pilih</th>
              <th>Nama</th>
              <th>Peran</th>
              <th>No. Telepon</th>
            </tr>
          </thead>
          <tbody>
            @foreach($sdms as $s)
            <tr>
              <td class="text-center">
                <input type="checkbox" name="sdm_ids[]"
                       value="{{ $s->id }}"
                       {{ $project->sdms->contains($s->id) ? 'checked' : '' }}>
              </td>
              <td>{{ $s->nama }}</td>
              <td>{{ $s->peran }}</td>
              <td>{{ $s->nomor_telepon }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      <button class="btn btn-maroon">Simpan SDM</button>
    </form>
  </div>
</div>
@endsection
