@extends('layouts.app')
@section('title','Material Proyek')

@section('content')
@php
  $role = auth()->user()->role;

  $isSiteManager = $role === 'site manager';
  $isAdmin       = $role === 'administrasi';
  $isKepalaLap   = $role === 'kepala lapangan';

  // yang boleh kelola estimasi + stok masuk manual
  $canManageMaterial = in_array($role, ['site manager','administrasi']);
@endphp

<div class="card">
  <div class="card-header d-flex align-items-center">
    <h5 class="mb-0">Material Proyek (Estimasi & Stok) - {{ $project->nama_proyek }}</h5>
    <a href="{{ route('project.progress.index',$project->id) }}" class="btn btn-secondary ml-auto">Kembali</a>
  </div>

  <div class="card-body">
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    @if($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    {{-- =========================================================
        SECTION ADMIN/SM: ESTIMASI + EVALUASI
       ========================================================= --}}
    @if($canManageMaterial)

      {{-- =============================
           FORM TAMBAH ESTIMASI
         ============================= --}}
      <h6 class="mb-2">Tambah Material Estimasi</h6>
      <form method="POST" action="{{ route('project.materials.store',$project->id) }}" class="mb-4">
        @csrf
        <div class="row">
          <div class="col-md-5">
            <label>Nama Material</label>
            <input type="text" name="nama_material" class="form-control" required>
          </div>
          <div class="col-md-2">
            <label>Satuan</label>
            <input type="text" name="satuan" class="form-control" placeholder="zak, pcs, m3">
          </div>
          <div class="col-md-3">
            <label>Qty Estimasi</label>
            <input type="number" step="0.01" name="qty_estimasi" class="form-control" required value="0">
          </div>
          <div class="col-md-2 d-flex align-items-end">
            <button class="btn btn-maroon btn-block">Tambah</button>
          </div>
        </div>
      </form>

      {{-- =============================
           TABEL EVALUASI ESTIMASI VS REALISASI
         ============================= --}}
      <h6 class="mb-2">Evaluasi Material (Estimasi vs Pakai)</h6>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th width="50">No</th>
            <th>Material</th>
            <th width="120">Satuan</th>
            <th width="140">Estimasi</th>
            <th width="140">Stok Masuk</th>
            <th width="140">Terpakai</th>
            <th width="140">Sisa Stok</th>
            <th width="120">Status Pakai</th>
            <th width="140">Status Stok</th>
            @if($isSiteManager)
              <th width="120">Aksi</th>
            @endif
          </tr>
        </thead>
        <tbody>
        @forelse($materials as $m)
          @php
            $estimasi = (float) ($m->qty_estimasi ?? 0);
            $masuk    = (float) ($m->qty_masuk_total ?? 0);
            $pakai    = (float) ($m->qty_pakai_total ?? 0);
            $tol      = (float) ($m->toleransi_persen ?? 0);

            $sisaStok     = $masuk - $pakai;

            $batasAman = $estimasi * 0.8;
            $batasOver = $estimasi * (1 + ($tol / 100));

            // status pakai
            if ($estimasi <= 0) {
              $statusPakai = 'Aman'; $badgePakai = 'success';
            } elseif ($pakai > $batasOver) {
              $statusPakai = 'Over'; $badgePakai = 'danger';
            } elseif ($pakai >= $estimasi) {
              $statusPakai = 'Habis'; $badgePakai = 'warning';
            } elseif ($pakai >= $batasAman) {
              $statusPakai = 'Hampir Habis'; $badgePakai = 'warning';
            } else {
              $statusPakai = 'Aman'; $badgePakai = 'success';
            }

            // status stok masuk
            if ($estimasi <= 0) {
              $statusStok = 'Aman'; $badgeStok = 'success';
            } else {
              if ($masuk > $batasOver) {
                $statusStok = 'Stok Melebihi Estimasi'; $badgeStok = 'danger';
              } else {
                $statusStok = 'Aman'; $badgeStok = 'success';
              }
            }
          @endphp

          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $m->nama_material }}</td>
            <td class="text-center">{{ $m->satuan ?? '-' }}</td>

            <td class="text-right">{{ number_format($estimasi, 2) }}</td>
            <td class="text-right">{{ number_format($masuk, 2) }}</td>
            <td class="text-right">{{ number_format($pakai, 2) }}</td>

            <td class="text-right">{{ number_format($sisaStok, 2) }}</td>

            <td class="text-center">
              <span class="badge badge-{{ $badgePakai }}">{{ $statusPakai }}</span>
            </td>
            <td class="text-center">
              <span class="badge badge-{{ $badgeStok }}">{{ $statusStok }}</span>
            </td>

            @if($isSiteManager)
              <td class="text-center">
                <form method="POST" action="{{ route('project.materials.destroy', [$project->id, $m->id]) }}">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus material ini?')">
                    Hapus
                  </button>
                </form>
              </td>
            @endif
          </tr>
        @empty
          <tr>
            <td colspan="{{ $isSiteManager ? 10 : 9 }}" class="text-center text-muted">
              Belum ada material estimasi
            </td>
          </tr>
        @endforelse
        </tbody>
      </table>

      <hr>

      {{-- =============================
           FORM STOK MASUK (SM/Admin)
         ============================= --}}
      <h6 class="mb-2">Tambah Stok Masuk Material</h6>
      <form method="POST" action="{{ route('project.materials.stock.store', $project->id) }}" class="mb-3">
        @csrf
        <div class="row">
          <div class="col-md-5">
            <label>Material</label>
            <select name="project_material_id" class="form-control" required>
              <option value="">-- pilih material --</option>
              @foreach($materials as $m)
                <option value="{{ $m->id }}">
                  {{ $m->nama_material }} {{ $m->satuan ? '(' . $m->satuan . ')' : '' }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-2">
            <label>Tanggal</label>
            <input type="date" name="tanggal" class="form-control" value="{{ date('Y-m-d') }}" required>
          </div>

          <div class="col-md-2">
            <label>Qty Masuk</label>
            <input type="number" step="0.01" name="qty_masuk" class="form-control" required>
          </div>

          <div class="col-md-3">
            <label>Catatan</label>
            <input type="text" name="catatan" class="form-control" placeholder="opsional">
          </div>
        </div>

        <small class="text-muted">
          Catatan wajib diisi jika stok masuk melebihi estimasi (+ toleransi).
        </small>

        <div class="mt-2">
          <button class="btn btn-maroon">Simpan Stok Masuk</button>
        </div>
      </form>

      {{-- ✅ RIWAYAT STOK MASUK - tepat di bawah tombol simpan --}}
      <h6 class="mb-2">Riwayat Stok Masuk</h6>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th width="150">Tanggal</th>
            <th>Material</th>
            <th width="160">Qty Masuk</th>
            <th>Catatan</th>
            @if($isSiteManager)
              <th width="120">Aksi</th>
            @endif
          </tr>
        </thead>
        <tbody>
          @forelse($stocks as $st)
            <tr>
              <td>{{ $st->tanggal }}</td>
              <td>{{ $st->projectMaterial->nama_material ?? '-' }}</td>
              <td class="text-right">
                {{ number_format((float)$st->qty_masuk, 2) }}
                {{ $st->projectMaterial->satuan ?? '' }}
              </td>
              <td>{{ $st->catatan ?? '-' }}</td>

              @if($isSiteManager)
                <td class="text-center">
                  <form method="POST" action="{{ route('project.materials.stock.destroy', [$project->id, $st->id]) }}">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus riwayat stok ini?')">Hapus</button>
                  </form>
                </td>
              @endif
            </tr>
          @empty
            <tr><td colspan="{{ $isSiteManager ? 5 : 4 }}" class="text-center text-muted">Belum ada stok masuk</td></tr>
          @endforelse
        </tbody>
      </table>

    @endif {{-- end canManageMaterial --}}

    {{-- =========================================================
        SECTION KEPALA LAPANGAN: RIWAYAT (READ ONLY)
        (kalau bukan SM/Admin)
       ========================================================= --}}
    @if($isKepalaLap && !$canManageMaterial)
      <h6 class="mb-2">Riwayat Stok Masuk</h6>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th width="150">Tanggal</th>
            <th>Material</th>
            <th width="160">Qty Masuk</th>
            <th>Catatan</th>
          </tr>
        </thead>
        <tbody>
          @forelse($stocks as $st)
            <tr>
              <td>{{ $st->tanggal }}</td>
              <td>{{ $st->projectMaterial->nama_material ?? '-' }}</td>
              <td class="text-right">
                {{ number_format((float)$st->qty_masuk, 2) }}
                {{ $st->projectMaterial->satuan ?? '' }}
              </td>
              <td>{{ $st->catatan ?? '-' }}</td>
            </tr>
          @empty
            <tr><td colspan="4" class="text-center text-muted">Belum ada stok masuk</td></tr>
          @endforelse
        </tbody>
      </table>

      <hr>
    @endif

    {{-- =========================================================
        SECTION PENGAJUAN
        - kepala lapangan: bisa input pengajuan
        - site manager: bisa approve/reject
       ========================================================= --}}
    <h6 class="mb-2">Pengajuan Material (Kepala Lapangan)</h6>

    @if($isKepalaLap)
      <form method="POST" action="{{ route('project.materials.request.store', $project->id) }}" class="mb-3">
        @csrf
        <div class="row">
          <div class="col-md-5">
            <label>Material</label>
            <select name="project_material_id" class="form-control" required>
              <option value="">-- pilih material --</option>
              @foreach($materials as $m)
                <option value="{{ $m->id }}">
                  {{ $m->nama_material }} {{ $m->satuan ? '(' . $m->satuan . ')' : '' }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-2">
            <label>Tanggal Pengajuan</label>
            <input type="date" name="tanggal_pengajuan" class="form-control" value="{{ date('Y-m-d') }}" required>
          </div>

          <div class="col-md-2">
            <label>Qty</label>
            <input type="number" step="0.01" name="qty" class="form-control" required>
          </div>

          <div class="col-md-3">
            <label>Catatan</label>
            <input type="text" name="catatan" class="form-control" placeholder="opsional">
          </div>
        </div>

        <div class="mt-2">
          <button class="btn btn-primary">Kirim Pengajuan</button>
        </div>
      </form>
    @else
      <small class="text-muted d-block mb-2">
        Form pengajuan hanya muncul untuk Kepala Lapangan.
      </small>
    @endif

    <h6 class="mb-2">Daftar Pengajuan Material</h6>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th width="60">No</th>
          <th>Tanggal</th>
          <th>Material</th>
          <th width="140">Qty</th>
          <th width="120">Status</th>
          <th>Catatan ACC/Tolak</th>
          @if($isSiteManager)
            <th width="220">Aksi</th>
          @endif
        </tr>
      </thead>
      <tbody>
        @forelse($requests as $r)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $r->tanggal_pengajuan?->format('Y-m-d') }}</td>
            <td>{{ $r->projectMaterial->nama_material ?? '-' }}</td>
            <td class="text-right">
              {{ number_format((float)$r->qty, 2) }}
              {{ $r->projectMaterial->satuan ?? '' }}
            </td>
            <td class="text-center">

              @if($r->status === 'pending')
                <span class="badge badge-warning">Pending</span>
              @elseif($r->status === 'approved')
                <span class="badge badge-success">Approved</span>
              @else
                <span class="badge badge-danger">Rejected</span>
              @endif
            </td>
            <td>{{ $r->approval_note ?? '-' }}</td>

            @if($isSiteManager)
              <td>
                @if($r->status === 'pending')
                  <form method="POST" action="{{ route('project.materials.request.approve', [$project->id, $r->id]) }}" class="mb-2">
                    @csrf
                    <input type="text" name="approval_note" class="form-control form-control-sm mb-1" placeholder="Catatan ACC (opsional)">
                    <button class="btn btn-sm btn-success" onclick="return confirm('ACC pengajuan ini? Otomatis menjadi stok masuk.')">
                      ACC
                    </button>
                  </form>

                  <form method="POST" action="{{ route('project.materials.request.reject', [$project->id, $r->id]) }}">
                    @csrf
                    <input type="text" name="approval_note" class="form-control form-control-sm mb-1" placeholder="Alasan tolak (wajib)" required>
                    <button class="btn btn-sm btn-danger" onclick="return confirm('Tolak pengajuan ini?')">
                      Tolak
                    </button>
                  </form>
                @else
                  <span class="text-muted">-</span>
                @endif
              </td>
            @endif
          </tr>
        @empty
          <tr>
            <td colspan="{{ $isSiteManager ? 7 : 6 }}" class="text-center text-muted">
              Belum ada pengajuan material.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>

  </div>
</div>
@endsection
