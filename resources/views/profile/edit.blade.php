@extends('layouts.app')
@section('title','Profil Saya')

@section('content')
<div class="card">
  <div class="card-header">
    <h5>Profil Saya</h5>
  </div>

  <div class="card-body">

    @if(session('success'))
      <div class="alert alert-success">
        {{ session('success') }}
      </div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach($errors->all() as $e)
            <li>{{ $e }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST"
          action="{{ route('profile.update') }}"
          autocomplete="off">

      @csrf
      @method('PUT')

      {{-- dummy anti autofill --}}
      <input type="text" style="display:none">
      <input type="password" style="display:none">

      <div class="mb-3">
        <label>Nama</label>
        <input
          type="text"
          name="name"
          class="form-control"
          value="{{ old('name', $user->name) }}"
          autocomplete="off"
          required
        >
      </div>

      <div class="mb-3">
        <label>Email</label>
        <input
          type="email"
          name="email"
          class="form-control"
          value="{{ old('email', $user->email) }}"
          autocomplete="new-email"
          required
        >
      </div>

      <hr>

      <h6 class="text-muted">Ganti Password (Opsional)</h6>

      <div class="mb-3">
        <label>Password Lama</label>
        <input
          type="password"
          name="current_password"
          class="form-control"
          autocomplete="current-password"
        >
      </div>

      <div class="mb-3">
        <label>Password Baru</label>
        <input
          type="password"
          name="password"
          class="form-control"
          autocomplete="new-password"
        >
      </div>

      <div class="mb-3">
        <label>Konfirmasi Password Baru</label>
        <input
          type="password"
          name="password_confirmation"
          class="form-control"
          autocomplete="new-password"
        >
      </div>

      <button type="submit" class="btn btn-maroon">
        Simpan Perubahan
      </button>

    </form>
  </div>
</div>
@endsection
