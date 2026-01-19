@extends('layouts.app')
@section('title','Tambah User')

@section('content')
<div class="card">
  <div class="card-header">
    <h5>Tambah User</h5>
  </div>

  <div class="card-body">

    {{-- TAMPILKAN ERROR GLOBAL --}}
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form method="POST"
          action="{{ route('user.store') }}"
          autocomplete="off"
          novalidate>

      @csrf

      {{-- 🔒 TRAP AUTOFILL BROWSER (PALING AMPUH) --}}
      <div style="position:absolute; left:-9999px; top:-9999px;">
        <input type="text" name="fake_user" autocomplete="username">
        <input type="password" name="fake_pass" autocomplete="current-password">
      </div>

      {{-- NAMA --}}
      <div class="mb-3">
        <label>Nama</label>
        <input
          type="text"
          name="name"
          class="form-control @error('name') is-invalid @enderror"
          value="{{ old('name') }}"
          autocomplete="off"
          autocapitalize="off"
          spellcheck="false"
          required
        >
        @error('name')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      {{-- EMAIL --}}
      <div class="mb-3">
        <label>Email</label>
        <input
          type="text"
          name="email"
          class="form-control @error('email') is-invalid @enderror"
          value="{{ old('email') }}"
          autocomplete="new-email"
          autocapitalize="off"
          spellcheck="false"
          required
        >
        @error('email')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>


      {{-- ROLE / HAK AKSES --}}
      <div class="mb-3">
        <label>Hak Akses</label>
          <select name="role" class="form-control" required>
            <option value="">-- Pilih Hak Akses --</option>
            <option value="site manager" {{ old('role')=='site manager'?'selected':'' }}>Site Manager</option>
            <option value="administrasi" {{ old('role')=='administrasi'?'selected':'' }}>Administrasi</option>
            <option value="kepala lapangan" {{ old('role')=='kepala lapangan'?'selected':'' }}>Kepala Lapangan</option>
          </select>

        @error('role')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>


      {{-- PASSWORD --}}
      <div class="mb-3">
        <label>Password</label>
        <input
          type="password"
          name="password"
          class="form-control @error('password') is-invalid @enderror"
          autocomplete="new-password"
          required
        >
        @error('password')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      {{-- KONFIRMASI PASSWORD --}}
      <div class="mb-3">
        <label>Konfirmasi Password</label>
        <input
          type="password"
          name="password_confirmation"
          class="form-control @error('password_confirmation') is-invalid @enderror"
          autocomplete="new-password"
          required
        >
        @error('password_confirmation')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      {{-- ACTION --}}
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
