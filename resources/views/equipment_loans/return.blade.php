@extends('layouts.app')
@section('title','Pengembalian Equipment')

@section('content')
<div class="card">
  <div class="card-header"><h5>Input Pengembalian</h5></div>
  <div class="card-body">

    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
    @if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

    <p><b>Proyek:</b> {{ $loan->project->nama_proyek }}</p>
    <p><b>Tanggal Pinjam:</b> {{ $loan->tanggal_pinjam }}</p>

    <form action="{{ route('equipment_loans.return.store',$loan->id) }}" method="POST">
      @csrf

      <div class="form-group">
        <label>Tanggal Kembali</label>
        <input type="date" name="tanggal_kembali" class="form-control" required value="{{ old('tanggal_kembali', date('Y-m-d')) }}">
      </div>

      <hr>
      <h6>Detail Pengembalian</h6>
      <small class="text-muted">Untuk tiap item: qty_baik + qty_rusak + qty_hilang harus sama dengan qty dipinjam.</small>

      <table class="table table-bordered mt-2">
        <thead>
          <tr>
            <th>Alat</th>
            <th width="80">Dipinjam</th>
            <th width="90">Baik</th>
            <th width="90">Rusak</th>
            <th width="90">Hilang</th>
            <th>Catatan</th>
          </tr>
        </thead>
        <tbody>
          @foreach($loan->items as $it)
          <tr>
            <td>{{ $it->equipment->nama_alat }}</td>
            <td class="text-center">{{ $it->qty }}</td>

            <td><input class="form-control" type="number" min="0" name="items[{{ $it->id }}][qty_baik]" value="{{ old('items.'.$it->id.'.qty_baik', $it->qty) }}" required></td>
            <td><input class="form-control" type="number" min="0" name="items[{{ $it->id }}][qty_rusak]" value="{{ old('items.'.$it->id.'.qty_rusak', 0) }}" required></td>
            <td><input class="form-control" type="number" min="0" name="items[{{ $it->id }}][qty_hilang]" value="{{ old('items.'.$it->id.'.qty_hilang', 0) }}" required></td>

            <td>
              <input class="form-control" type="text" name="items[{{ $it->id }}][catatan_kondisi]"
                     value="{{ old('items.'.$it->id.'.catatan_kondisi') }}">
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>

      <div class="mt-4">
        <button class="btn btn-maroon" onclick="return confirm('Simpan pengembalian?')">Simpan Pengembalian</button>
        <a href="{{ route('equipment_loans.show',$loan->id) }}" class="btn btn-secondary">Kembali</a>
      </div>
    </form>
  </div>
</div>
@endsection
