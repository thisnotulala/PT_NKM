@extends('layouts.app')

@section('title', 'Edit SDM')

@section('content')
<div class="card">
    <div class="card-header">
        <h5>Edit SDM</h5>
    </div>

    <div class="card-body">

        {{-- ERROR VALIDASI --}}
        @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('sdm.update', $sdm->id) }}" method="POST" autocomplete="off">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label>Nama</label>
                <input type="text"
                       name="nama"
                       class="form-control"
                       required
                       value="{{ old('nama', $sdm->nama) }}">
            </div>

            <div class="form-group mt-3">
                <label>Peran</label>
                <input type="text"
                       name="peran"
                       class="form-control"
                       required
                       value="{{ old('peran', $sdm->peran) }}">
            </div>

            <div class="form-group mt-3">
                <label>Nomor Telepon (opsional)</label>
                <input type="text"
                       name="nomor_telepon"
                       class="form-control"
                       inputmode="numeric"
                       pattern="[0-9]*"
                       oninput="this.value=this.value.replace(/[^0-9]/g,'')"
                       placeholder="Contoh: 08123456789"
                       value="{{ old('nomor_telepon', $sdm->nomor_telepon) }}">
            </div>

            <div class="form-group mt-3">
                <label>Alamat (opsional)</label>
                <textarea name="alamat"
                          class="form-control"
                          rows="3">{{ old('alamat', $sdm->alamat) }}</textarea>
            </div>

            <div class="mt-4">
                <button class="btn btn-maroon">Update</button>
                <a href="{{ route('sdm.index') }}" class="btn btn-secondary">Kembali</a>
            </div>
        </form>
    </div>
</div>
@endsection
