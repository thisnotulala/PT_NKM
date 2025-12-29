@extends('layouts.app')
@section('title','Dashboard')

@section('content')
<div class="container-fluid">

  <div class="row">
    <div class="col-md-3">
      <div class="card p-3">
        <div class="stat-card">
          <div>
            <div class="text-muted">Total Proyek</div>
            <h3 class="mb-0">{{ $totalProjects }}</h3>
          </div>
          <i class="fas fa-folder-open"></i>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card p-3">
        <div class="stat-card">
          <div>
            <div class="text-muted">Proyek Aktif</div>
            <h3 class="mb-0">{{ $aktifProjects }}</h3>
          </div>
          <i class="fas fa-play-circle"></i>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card p-3">
        <div class="stat-card">
          <div>
            <div class="text-muted">Selesai</div>
            <h3 class="mb-0">{{ $selesaiProjects }}</h3>
          </div>
          <i class="fas fa-check-circle"></i>
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card p-3">
        <div class="stat-card">
          <div>
            <div class="text-muted">Terlambat</div>
            <h3 class="mb-0">{{ $telatProjects }}</h3>
          </div>
          <i class="fas fa-exclamation-triangle"></i>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-3">
    <div class="col-md-8">
      <div class="card">
        <div class="card-header"><b>Progress Proyek Aktif (%)</b></div>
        <div class="card-body">
          <canvas id="barProgress" height="120"></canvas>
          @if(count($barLabels) == 0)
            <small class="text-muted d-block mt-2">Tidak ada proyek aktif.</small>
          @endif
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card">
        <div class="card-header"><b>Status Proyek</b></div>
        <div class="card-body">
          <canvas id="pieStatus" height="220"></canvas>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-3">
    <div class="col-12">
      <div class="card">
        <div class="card-header"><b>Proyek Butuh Perhatian</b></div>
        <div class="card-body">
          <table class="table table-bordered table-hover">
            <thead>
              <tr>
                <th width="50">No</th>
                <th>Proyek</th>
                <th width="180">Client</th>
                <th width="220">Durasi</th>
                <th width="120">Progress</th>
                <th width="130">Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($attention as $a)
              <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $a->nama_proyek }}</td>
                <td>{{ $a->client }}</td>
                <td>{{ $a->tanggal_mulai }} s/d {{ $a->tanggal_selesai }}</td>
                <td class="text-center">{{ $a->progress_total }}%</td>
                <td class="text-center">
                  <span class="badge badge-{{ $a->status=='Terlambat' ? 'danger' : 'warning' }}">
                    {{ $a->status }}
                  </span>
                </td>
              </tr>
              @empty
              <tr><td colspan="6" class="text-center">Tidak ada yang perlu perhatian.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>

{{-- Chart.js --}}
<script src="{{ asset('adminlte/plugins/chart.js/Chart.min.js') }}"></script>

<script>
  const barLabels = @json($barLabels);
  const barData   = @json($barData);

  const pieLabels = @json($pieLabels);
  const pieData   = @json($pieData);

  // Bar Chart - Progress proyek aktif
  const ctxBar = document.getElementById('barProgress');
  if (ctxBar) {
    new Chart(ctxBar, {
      type: 'bar',
      data: {
        labels: barLabels,
        datasets: [{
          label: 'Progress (%)',
          data: barData,
          backgroundColor: 'rgba(139, 30, 30, 0.6)', // maroon
          borderColor: 'rgba(139, 30, 30, 1)',
          borderWidth: 1,
          borderRadius: 8
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: { beginAtZero: true, max: 100 }
        }
      }
    });
  }

  // Pie Chart - Status proyek
  const ctxPie = document.getElementById('pieStatus');
  if (ctxPie) {
    new Chart(ctxPie, {
      type: 'doughnut',
      data: {
        labels: pieLabels,
        datasets: [{
          data: pieData,
          backgroundColor: [
            'rgba(108, 117, 125, 0.7)', // Belum Mulai
            'rgba(23, 162, 184, 0.7)',  // Aktif
            'rgba(40, 167, 69, 0.7)',   // Selesai
            'rgba(220, 53, 69, 0.7)'    // Terlambat
          ],
          borderColor: [
            'rgba(108, 117, 125, 1)',
            'rgba(23, 162, 184, 1)',
            'rgba(40, 167, 69, 1)',
            'rgba(220, 53, 69, 1)'
          ],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'top' }
        }
      }
    });
  }
</script>
@endsection
