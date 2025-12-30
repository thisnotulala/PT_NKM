@extends('layouts.app')
@section('title','User')

@section('content')
<div class="card">

  {{-- HEADER --}}
  <div class="card-header">
    <div class="row align-items-center w-100">
      <div class="col">
        <h5 class="mb-0">Manajemen User</h5>
      </div>

      <div class="col-auto text-right">
        <a href="{{ route('user.create') }}" class="btn btn-maroon btn-sm">
          <i class="fas fa-plus mr-1"></i> Tambah User
        </a>
      </div>
    </div>
  </div>

  {{-- BODY --}}
  <div class="card-body">

    @if(session('success'))
      <div class="alert alert-success">
        {{ session('success') }}
      </div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger">
        {{ session('error') }}
      </div>
    @endif

    <div class="table-responsive">
      <table class="table table-bordered table-hover mb-0">
        <thead>
          <tr>
            <th width="50" class="text-center">No</th>
            <th>Nama</th>
            <th>Email</th>
            <th width="120" class="text-center">Aksi</th>
          </tr>
        </thead>

        <tbody>
          @forelse($users as $u)
          <tr>
            <td class="text-center">{{ $loop->iteration }}</td>
            <td>{{ $u->name }}</td>
            <td>{{ $u->email }}</td>

            {{-- AKSI --}}
            <td class="text-center">
              <div class="action-group">

                {{-- EDIT --}}
                <a href="{{ route('user.edit', $u->id) }}"
                   class="btn-action btn-edit"
                   title="Edit User">
                  <i class="fas fa-edit"></i>
                </a>

                {{-- DELETE --}}
                @if(auth()->id() !== $u->id)
                <form action="{{ route('user.destroy', $u->id) }}"
                      method="POST"
                      onsubmit="return confirm('Hapus user ini?')">
                  @csrf
                  @method('DELETE')

                  <button type="submit"
                          class="btn-action btn-delete"
                          title="Hapus User">
                    <i class="fas fa-trash"></i>
                  </button>
                </form>
                @endif

              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="4" class="text-center text-muted">
              Belum ada user
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

  </div>
</div>
@endsection
