@extends('layouts.app')
@section('title','Detail Peminjaman')

@section('content')
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h5 class="mb-0">Detail Peminjaman</h5>
    <a href="{{ route('equipment_loans.index') }}" class="btn btn-secondary ml-auto">Kembali</a>
  </div>

  <div class="card-body">
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <p><b>Proyek:</b> {{ $loan->project->nama_proyek ?? '-' }}</p>
    <p><b>Tanggal Pinjam:</b> {{ $loan->tanggal_pinjam }}</p>
    <p><b>Status:</b> {{ strtoupper($loan->status) }}</p>
    @if($loan->catatan)<p><b>Catatan:</b> {{ $loan->catatan }}</p>@endif

    <hr>
    <h6>Item</h6>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Alat</th>
          <th width="90">Qty</th>
          <th width="90">Baik</th>
          <th width="90">Rusak</th>
          <th width="90">Hilang</th>
        </tr>
      </thead>
      <tbody>
        @foreach($loan->items as $it)
        <tr>
          <td>{{ $it->equipment->nama_alat }} ({{ $it->equipment->satuan->nama_satuan ?? '' }})</td>
          <td class="text-center">{{ $it->qty }}</td>
          <td class="text-center">{{ $it->qty_baik }}</td>
          <td class="text-center">{{ $it->qty_rusak }}</td>
          <td class="text-center">{{ $it->qty_hilang }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    <div class="mt-3 d-flex" style="gap:8px; flex-wrap:wrap;">
      @if($loan->status=='pending')
        <form method="POST" action="{{ route('equipment_loans.approve',$loan->id) }}">
          @csrf
          <button class="btn btn-success" onclick="return confirm('Approve peminjaman?')">Approve</button>
        </form>

        <form method="POST" action="{{ route('equipment_loans.reject',$loan->id) }}">
          @csrf
          <button class="btn btn-danger" onclick="return confirm('Reject peminjaman?')">Reject</button>
        </form>
      @endif

      @if($loan->status=='approved')
        <a href="{{ route('equipment_loans.return.form',$loan->id) }}" class="btn btn-primary">
          Input Pengembalian
        </a>
      @endif
    </div>
  </div>
</div>
@endsection
