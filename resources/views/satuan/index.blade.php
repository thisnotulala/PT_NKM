@extends('layouts.app')
@section('title', 'Master Satuan')

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center">
        <h5 class="mb-0">Master Satuan</h5>

        <a href="{{ route('satuan.create') }}" class="btn btn-maroon ml-auto">
            <i class="fas fa-plus"></i> Tambah Satuan
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
                    <th>Nama Satuan</th>
                    <th width="80">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($satuans as $satuan)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $satuan->nama_satuan }}</td>
                        <td>
                            <div class="action-group">
                                <a href="{{ route('satuan.edit', $satuan->id) }}"
                                   class="btn-action btn-edit"
                                   title="Edit">
                                    <i class="fas fa-pen"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center">
                            Data satuan kosong
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
