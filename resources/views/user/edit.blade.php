@extends('layouts.app')
@section('title','Edit User')

@section('content')
<div class="card">
  <div class="card-header">
    <h5>Edit User</h5>
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

    <form method="POST"
          action="{{ route('user.update', $user->id) }}"
          novalidate>
      @csrf
      @method('PUT')

      {{-- NAMA --}}
      <div class="mb-3">
        <label>Nama</label>
        <input type="text"
               name="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $user->name) }}"
               required>
        @error('name')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      {{-- EMAIL --}}
      <div class="mb-3">
        <label>Email</label>
        <input type="text"
               name="email"
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $user->email) }}"
               required>
        @error('email')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      {{-- PASSWORD BARU --}}
      <div class="mb-3">
        <label>Password Baru <small class="text-muted">(opsional)</small></label>
        <input type="password"
               name="password"
               class="form-control @error('password') is-invalid @enderror">
        @error('password')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      {{-- KONFIRMASI PASSWORD --}}
      <div class="mb-3">
        <label>Konfirmasi Password</label>
        <input type="password"
               name="password_confirmation"
               class="form-control @error('password_confirmation') is-invalid @enderror">
        @error('password_confirmation')
          <div class="invalid-feedback">{{ $message }}</div>
        @enderror
      </div>

      <button class="btn btn-maroon">
        Update
      </button>

      <a href="{{ route('user.index') }}" class="btn btn-secondary">
        Kembali
      </a>
    </form>

  </div>
</div>
@endsection
