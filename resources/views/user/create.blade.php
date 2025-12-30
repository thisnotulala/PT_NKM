@extends('layouts.app')
@section('title','Tambah User')

@section('content')
<div class="card">
  <div class="card-header">
    <h5>Tambah User</h5>
  </div>

  <div class="card-body">

    <form method="POST"
          action="{{ route('user.store') }}"
          autocomplete="off">

      @csrf

      {{-- DUMMY INPUT ANTI AUTOFILL (BIAR BROWSER KEJEBAK) --}}
      <input type="text" style="display:none">
      <input type="password" style="display:none">

      <div class="mb-3">
        <label>Nama</label>
        <input
          type="text"
          name="name"
          class="form-control"
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
          autocomplete="new-email"
          required
        >
      </div>

      <div class="mb-3">
        <label>Password</label>
        <input
          type="password"
          name="password"
          class="form-control"
          autocomplete="new-password"
          required
        >
      </div>

      <div class="mb-3">
        <label>Konfirmasi Password</label>
        <input
          type="password"
          name="password_confirmation"
          class="form-control"
          autocomplete="new-password"
          required
        >
      </div>

      <button type="submit" class="btn btn-maroon">
        Simpan
      </button>

      <a href="{{ route('user.index') }}" class="btn btn-secondary">
        Kembali
      </a>

    </form>

  </div>
</div>
@endsection
