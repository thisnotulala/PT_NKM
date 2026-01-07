@extends('layouts.app')
@section('title','Detail Peminjaman')

@section('content')
@php
  $role = auth()->user()->role;
@endphp

<div class="card">
  <div class="card-header d-flex align-items-center">
    <h5 class="mb-0">Detail Peminjaman Equipment</h5>
    <a href="{{ route('equipment_loans.index') }}" class="btn btn-secondary ml-auto">
      Kembali
    </a>
  </div>

  <div class="card-body">

    {{-- ALERT --}}
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- INFO PEMINJAMAN --}}
    <p><b>Proyek:</b> {{ $loan->project->nama_proyek ?? '-' }}</p>
    <p><b>Tanggal Pinjam:</b> {{ $loan->tanggal_pinjam }}</p>
    <p>
      <b>Status:</b>
      @if($loan->status === 'pending')
        <span class="badge badge-warning">Pending</span>
      @elseif($loan->status === 'approved')
        <span class="badge badge-success">Approved</span>
      @elseif($loan->status === 'rejected')
        <span class="badge badge-danger">Rejected</span>
      @elseif($loan->status === 'returned')
        <span class="badge badge-info">Returned</span>
      @endif
    </p>

    @if($loan->catatan)
      <p><b>Catatan:</b> {{ $loan->catatan }}</p>
    @endif

    <hr>

    {{-- ITEM PEMINJAMAN --}}
    <h6>Detail Alat</h6>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th>Alat</th>
          <th width="90">Dipinjam</th>
          <th width="90">Baik</th>
          <th width="90">Rusak</th>
          <th width="90">Hilang</th>
        </tr>
      </thead>
      <tbody>
        @foreach($loan->items as $it)
        <tr>
          <td>
            {{ $it->equipment->nama_alat }}
            ({{ $it->equipment->satuan->nama_satuan ?? '-' }})
          </td>
          <td class="text-center">{{ $it->qty }}</td>
          <td class="text-center">{{ $it->qty_baik ?? '-' }}</td>
          <td class="text-center">{{ $it->qty_rusak ?? '-' }}</td>
          <td class="text-center">{{ $it->qty_hilang ?? '-' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>

    {{-- AKSI --}}
    <div class="mt-3 d-flex flex-wrap" style="gap:8px">

      {{-- APPROVE / REJECT --}}
      @if(
        $loan->status === 'pending' &&
        in_array($role, ['site manager','administrasi'])
      )
        <form method="POST" action="{{ route('equipment_loans.approve',$loan->id) }}">
          @csrf
          <button class="btn btn-success"
            onclick="return confirm('Setujui peminjaman ini?')">
            Approve
          </button>
        </form>

        <form method="POST" action="{{ route('equipment_loans.reject',$loan->id) }}">
          @csrf
          <button class="btn btn-danger"
            onclick="return confirm('Tolak peminjaman ini?')">
            Reject
          </button>
        </form>
      @endif

      {{-- INPUT PENGEMBALIAN --}}
      @if(
        $loan->status === 'approved' &&
        $role === 'kepala lapangan'
      )
        <a href="{{ route('equipment_loans.return.form',$loan->id) }}"
           class="btn btn-primary">
          Input Pengembalian
        </a>
      @endif

    </div>

  </div>
</div>
@endsection
