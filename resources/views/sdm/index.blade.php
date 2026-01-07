@extends('layouts.app')

@section('title', 'Data SDM')

@section('content')
<div class="card">
    <div class="card-header d-flex align-items-center">
        <h5 class="mb-0">Data SDM</h5>

        <a href="{{ route('sdm.create') }}" class="btn btn-maroon ml-auto">
            <i class="fas fa-plus"></i> Tambah SDM
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
                    <th>Nama</th>
                    <th>Peran</th>
                    <th>No. Telepon</th>
                    <th>Alamat</th>
                    <th width="140">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sdms as $sdm)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $sdm->nama }}</td>
                    <td>{{ $sdm->peran }}</td>
                    <td>{{ $sdm->nomor_telepon }}</td>
                    <td>{{ $sdm->alamat }}</td>
                    <td>
                        <div class="action-group">
                            <a href="{{ route('sdm.edit', $sdm->id) }}"
                               class="btn-action btn-edit"
                               title="Edit SDM">
                                <i class="fas fa-pen"></i>
                            </a>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">Data SDM kosong</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
