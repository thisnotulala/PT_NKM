@extends('layouts.app')
@section('title','Material Proyek')

@section('content')
<div class="card">
  <div class="card-header d-flex align-items-center">
    <h5 class="mb-0">Material Proyek (Estimasi & Stok) - {{ $project->nama_proyek }}</h5>
    <a href="{{ route('project.progress.index',$project->id) }}" class="btn btn-secondary ml-auto">Kembali</a>
  </div>

  <div class="card-body">
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
    @if($errors->any()) <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

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
          <th width="140">Sisa Estimasi</th>
          <th width="140">Sisa Stok</th>
          <th width="120">Status</th>
          <th width="120">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($materials as $m)
          @php
            // ✅ hitungan dasar
            $estimasi = (float) ($m->qty_estimasi ?? 0);
            $masuk    = (float) ($m->qty_masuk_total ?? 0);  // dari withSum stocks
            $pakai    = (float) ($m->qty_pakai_total ?? 0);  // dari withSum usages
            $tol      = (float) ($m->toleransi_persen ?? 0);

            $sisaEstimasi = $estimasi - $pakai;
            $sisaStok     = $masuk - $pakai;

            // ✅ batas status
            $batasAman = $estimasi * 0.8;
            $batasOver = $estimasi * (1 + ($tol / 100));

            // ✅ status
            if ($estimasi <= 0) {
              $status = 'Aman';
              $badge  = 'success';
            } elseif ($pakai > $batasOver) {
              $status = 'Over';
              $badge  = 'danger';
            } elseif ($pakai >= $estimasi) {
              // pakai == estimasi -> habis, pakai > estimasi -> harusnya sudah kena Over di atas
              $status = 'Habis';
              $badge  = 'warning';
            } elseif ($pakai >= $batasAman) {
              $status = 'Hampir Habis';
              $badge  = 'warning';
            } else {
              $status = 'Aman';
              $badge  = 'success';
            }
          @endphp

          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $m->nama_material }}</td>
            <td class="text-center">{{ $m->satuan ?? '-' }}</td>

            <td class="text-right">{{ number_format($estimasi, 2) }}</td>
            <td class="text-right">{{ number_format($masuk, 2) }}</td>
            <td class="text-right">{{ number_format($pakai, 2) }}</td>

            <td class="text-right">{{ number_format($sisaEstimasi, 2) }}</td>
            <td class="text-right">{{ number_format($sisaStok, 2) }}</td>

            <td class="text-center">
              <span class="badge badge-{{ $badge }}">{{ $status }}</span>
            </td>

            <td class="text-center">
              @if(auth()->user()->role === 'site manager')
                <form method="POST" action="{{ route('project.materials.destroy', [$project->id, $m->id]) }}">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus material ini?')">Hapus</button>
                </form>
              @else
                <span class="text-muted">-</span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="10" class="text-center text-muted">Belum ada material estimasi</td></tr>
        @endforelse
      </tbody>
    </table>

    <hr>

    {{-- =============================
         FORM STOK MASUK
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

      <div class="mt-2">
        <button class="btn btn-maroon">Simpan Stok Masuk</button>
      </div>
    </form>

    {{-- =============================
         RIWAYAT STOK MASUK
       ============================= --}}
    <h6 class="mb-2">Riwayat Stok Masuk</h6>
    <table class="table table-bordered">
      <thead>
        <tr>
          <th width="150">Tanggal</th>
          <th>Material</th>
          <th width="160">Qty Masuk</th>
          <th>Catatan</th>
          <th width="120">Aksi</th>
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
            <td class="text-center">
              @if(auth()->user()->role === 'site manager')
                <form method="POST" action="{{ route('project.materials.stock.destroy', [$project->id, $st->id]) }}">
                  @csrf
                  @method('DELETE')
                  <button class="btn btn-sm btn-danger" onclick="return confirm('Hapus riwayat stok ini?')">Hapus</button>
                </form>
              @else
                <span class="text-muted">-</span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center text-muted">Belum ada stok masuk</td></tr>
        @endforelse
      </tbody>
    </table>

  </div>
</div>
@endsection
