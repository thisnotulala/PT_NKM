@extends('layouts.app')
@section('title','Peminjaman Equipment')

@section('content')
@php
  $role = auth()->user()->role;
@endphp

<div class="card">
  <div class="card-header d-flex align-items-center">
    <h5 class="mb-0">Peminjaman Equipment</h5>

    {{-- TOMBOL AJUKAN PEMINJAMAN --}}
    @if($role === 'kepala lapangan')
      <a href="{{ route('equipment_loans.create') }}" class="btn btn-maroon ml-auto">
        <i class="fas fa-plus"></i> Ajukan Peminjaman
      </a>
    @endif
  </div>

  <div class="card-body">

    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-bordered table-hover">
      <thead>
        <tr>
          <th width="50">No</th>
          <th>Proyek</th>
          <th width="140">Tanggal Pinjam</th>
          <th width="120">Status</th>
          <th width="120">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($loans as $l)
        <tr>
          <td>{{ $loop->iteration }}</td>
          <td>{{ $l->project->nama_proyek ?? '-' }}</td>
          <td>{{ $l->tanggal_pinjam }}</td>
          <td class="text-center">
            @if($l->status == 'pending')
              <span class="badge badge-warning">Pending</span>
            @elseif($l->status == 'approved')
              <span class="badge badge-success">Approved</span>
            @elseif($l->status == 'rejected')
              <span class="badge badge-danger">Rejected</span>
            @elseif($l->status == 'returned')
              <span class="badge badge-info">Returned</span>
            @endif
          </td>
          <td class="text-center">
            <a class="btn btn-sm btn-primary"
               href="{{ route('equipment_loans.show',$l->id) }}">
              Detail
            </a>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center text-muted">
            Belum ada peminjaman equipment
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>

  </div>
</div>
@endsection
