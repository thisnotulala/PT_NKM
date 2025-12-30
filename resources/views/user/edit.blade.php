@extends('layouts.app')
@section('title','Edit User')

@section('content')
<div class="card">
  <div class="card-header"><h5>Edit User</h5></div>
  <div class="card-body">
    <form method="POST" action="{{ route('user.update',$user->id) }}">
      @csrf @method('PUT')

      <div class="mb-3">
        <label>Nama</label>
        <input name="name" class="form-control" value="{{ $user->name }}" required>
      </div>

      <div class="mb-3">
        <label>Email</label>
        <input name="email" type="email" class="form-control" value="{{ $user->email }}" required>
      </div>

      <div class="mb-3">
        <label>Password Baru (opsional)</label>
        <input name="password" type="password" class="form-control">
      </div>

      <div class="mb-3">
        <label>Konfirmasi Password</label>
        <input name="password_confirmation" type="password" class="form-control">
      </div>

      <button class="btn btn-maroon">Update</button>
      <a href="{{ route('user.index') }}" class="btn btn-secondary">Kembali</a>
    </form>
  </div>
</div>
@endsection
