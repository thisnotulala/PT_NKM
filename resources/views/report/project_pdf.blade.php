<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Laporan Proyek</title>

  <style>
    @page { margin: 24px 28px; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; }

    .header {
      border-bottom: 2px solid #8B1E1E;
      padding-bottom: 10px;
      margin-bottom: 14px;
    }
    .brand {
      width: 100%;
      border-collapse: collapse;
    }
    .brand td { vertical-align: middle; }
    .logo {
      width: 72px;
      height: 72px;
      border-radius: 6px;
      object-fit: cover;
    }
    .company-name {
      font-size: 16px;
      font-weight: bold;
      color: #8B1E1E;
      margin: 0;
      line-height: 1.2;
    }
    .company-sub {
      font-size: 11px;
      color: #555;
      margin-top: 2px;
    }
    .report-title {
      text-align: right;
    }
    .report-title .t1 { font-size: 14px; font-weight: bold; margin: 0; }
    .report-title .t2 { font-size: 10px; color:#666; margin-top: 3px; }

    .badge {
      display: inline-block;
      padding: 3px 8px;
      border-radius: 999px;
      font-size: 10px;
      color: #fff;
    }
    .badge-info { background:#0ea5e9; }
    .badge-success { background:#16a34a; }
    .badge-warning { background:#f59e0b; }
    .badge-danger { background:#dc2626; }

    .grid {
      width: 100%;
      border-collapse: collapse;
      margin-top: 8px;
    }
    .grid td {
      padding: 6px 8px;
      border: 1px solid #ddd;
    }
    .grid td.label {
      width: 26%;
      background: #f8f8f8;
      font-weight: bold;
    }

    .section {
      margin-top: 14px;
      font-weight: bold;
      font-size: 12px;
      color: #8B1E1E;
      border-left: 4px solid #8B1E1E;
      padding-left: 8px;
    }

    table.tbl {
      width: 100%;
      border-collapse: collapse;
      margin-top: 8px;
    }
    table.tbl th, table.tbl td {
      border: 1px solid #ddd;
      padding: 6px 6px;
    }
    table.tbl th {
      background: #8B1E1E;
      color: #fff;
      font-size: 10px;
      text-transform: uppercase;
      letter-spacing: .3px;
    }
    .text-center { text-align: center; }
    .text-right { text-align: right; }
    .muted { color: #666; }

    .footer {
      position: fixed;
      bottom: -8px;
      left: 0;
      right: 0;
      font-size: 9px;
      color: #777;
      border-top: 1px solid #ddd;
      padding-top: 6px;
    }
  </style>
</head>
<body>

  {{-- HEADER --}}
  <div class="header">
    <table class="brand">
      <tr>
        <td style="width:90px;">
          @if(!empty($logoBase64))
            <img class="logo" src="{{ $logoBase64 }}" alt="Logo">
          @endif
        </td>

        <td>
          <p class="company-name">PT. NUSANTARA KLIK MAKMUR</p>
          <div class="company-sub">Sistem Informasi Proyek (SIP Proyek)</div>
        </td>

        <td class="report-title" style="width:220px;">
          <p class="t1">Laporan Proyek</p>
          <div class="t2">
            Dicetak: {{ date('Y-m-d H:i') }}
          </div>
        </td>
      </tr>
    </table>
  </div>

  {{-- RINGKASAN --}}
  <table class="grid">
    <tr>
      <td class="label">Nama Proyek</td>
      <td>{{ $project->nama_proyek }}</td>
      <td class="label">Status</td>
      <td>
        @php
          $badge = 'badge-info';
          if ($status === 'Selesai') $badge = 'badge-success';
          elseif ($status === 'Terlambat') $badge = 'badge-danger';
          elseif ($status === 'Belum Mulai') $badge = 'badge-warning';
        @endphp
        <span class="badge {{ $badge }}">{{ $status }}</span>
      </td>
    </tr>
    <tr>
      <td class="label">Client</td>
      <td>{{ $project->client->nama ?? '-' }}</td>
      <td class="label">Durasi</td>
      <td>{{ $project->tanggal_mulai }} s/d {{ $project->tanggal_selesai }}</td>
    </tr>
    <tr>
      <td class="label">Progress Total</td>
      <td>{{ number_format($progressTotal, 1) }}%</td>
      <td class="label">Total Pengeluaran</td>
      <td>Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</td>
    </tr>
    <tr>
      <td class="label">RAB</td>
      <td colspan="3">
        @if($project->rab_path)
          Ada (terupload)
        @else
          <span class="muted">Belum diupload</span>
        @endif
      </td>
    </tr>
  </table>

  {{-- A. TAHAPAN --}}
  <div class="section">A. Tahapan & Progress</div>
  <table class="tbl">
    <thead>
      <tr>
        <th width="50">No</th>
        <th>Tahapan</th>
        <th width="80">Bobot</th>
        <th width="80">Progress</th>
        <th width="120">Update Terakhir</th>
      </tr>
    </thead>
    <tbody>
      @forelse($project->phases as $ph)
      <tr>
        <td class="text-center">{{ $loop->iteration }}</td>
        <td>{{ $ph->nama_tahapan }}</td>
        <td class="text-center">{{ $ph->persen }}%</td>
        <td class="text-center">{{ $ph->progress ?? 0 }}%</td>
        <td class="text-center">
          @if($ph->last_progress_at)
            {{ $ph->last_progress_at }}
          @else
            <span class="muted">-</span>
          @endif
        </td>
      </tr>
      @empty
      <tr><td colspan="5" class="text-center muted">Belum ada tahapan</td></tr>
      @endforelse
    </tbody>
  </table>

  {{-- B. SDM --}}
  <div class="section">B. Tim SDM</div>
  <table class="tbl">
    <thead>
      <tr>
        <th width="50">No</th>
        <th>Nama</th>
        <th width="160">Peran (Master)</th>
        <th width="180">Peran di Proyek</th>
      </tr>
    </thead>
    <tbody>
      @forelse($project->projectSdms as $as)
      <tr>
        <td class="text-center">{{ $loop->iteration }}</td>
        <td>{{ $as->sdm->nama ?? '-' }}</td>
        <td>{{ $as->sdm->peran ?? '-' }}</td>
        <td>{{ $as->peran_di_proyek ?? '-' }}</td>
      </tr>
      @empty
      <tr><td colspan="4" class="text-center muted">Belum ada SDM</td></tr>
      @endforelse
    </tbody>
  </table>

  {{-- C. LOG PROGRESS --}}
  <div class="section">C. Riwayat Progress</div>
  <table class="tbl">
    <thead>
      <tr>
        <th width="95">Tanggal</th>
        <th width="200">Tahapan</th>
        <th width="80">Progress</th>
        <th>Catatan</th>
        <th width="80">Foto</th>
      </tr>
    </thead>
    <tbody>
      @forelse($logs as $l)
      <tr>
        <td class="text-center">{{ $l->tanggal_update }}</td>
        <td>{{ $l->phase->nama_tahapan ?? '-' }}</td>
        <td class="text-center">{{ $l->progress }}%</td>
        <td>{{ $l->catatan ?? '-' }}</td>
        <td class="text-center">{{ $l->photos->count() }}</td>
      </tr>
      @empty
      <tr><td colspan="5" class="text-center muted">Belum ada progress</td></tr>
      @endforelse
    </tbody>
  </table>

  <div class="footer">
    PT. Nusantara Klik Makmur — Laporan Proyek: {{ $project->nama_proyek }}
  </div>

</body>
</html>
