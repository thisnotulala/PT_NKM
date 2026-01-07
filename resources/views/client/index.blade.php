@extends('layouts.app')

@section('title', 'Data Client')

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center">
        <h5 class="mb-0">Data Client</h5>

        <a href="{{ route('client.create') }}"
           class="btn btn-maroon ml-auto">
            <i class="fas fa-plus"></i> Tambah Client
        </a>
    </div>

    <div class="card-body">
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th width="50">No</th>
                    <th>Nama Client</th>
                    <th>Alamat</th>
                    <th>No. Telepon</th>
                    <th width="80">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $client->nama }}</td>
                    <td>{{ $client->alamat ?? '-' }}</td>
                    <td>{{ $client->nomor_telepon }}</td>
                    <td>
                        <div class="action-group">
                            <a href="{{ route('client.edit', $client->id) }}"
                               class="btn-action btn-edit"
                               title="Edit Client">
                                <i class="fas fa-pen"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center">
                        Data client kosong
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
