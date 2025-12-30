<aside class="main-sidebar elevation-4">
    <a href="#" class="brand-link">
        Nusantara Klik Makmur
    </a>

    <div class="sidebar">
        <nav>
            <ul class="nav nav-pills nav-sidebar flex-column">

                <!-- DASHBOARD -->
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                       class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- MASTER DATA -->
                <li class="nav-header">MASTER DATA</li>

                <!-- CLIENT -->
                <li class="nav-item">
                    <a href="{{ route('client.index') }}"
                       class="nav-link {{ request()->is('client*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-building"></i>
                        <p>Client</p>
                    </a>
                </li>

                <!-- SDM -->
                <li class="nav-item">
                    <a href="{{ route('sdm.index') }}"
                       class="nav-link {{ request()->is('sdm*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>SDM</p>
                    </a>
                </li>

                <!-- SATUAN -->
                <li class="nav-item">
                    <a href="{{ route('satuan.index') }}"
                       class="nav-link {{ request()->is('satuan*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-ruler-combined"></i>
                        <p>Satuan</p>
                    </a>
                </li>

                <!-- EQUIPMENT -->
                <li class="nav-item">
                    <a href="{{ route('equipment.index') }}"
                       class="nav-link {{ request()->is('equipment*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-toolbox"></i>
                        <p>Equipment</p>
                    </a>
                </li>

                <!-- PROYEK -->
                <li class="nav-header">PROYEK</li>

                <li class="nav-item">
                    <a href="{{ route('project.index') }}"
                       class="nav-link {{ request()->is('project*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-project-diagram"></i>
                        <p>Proyek</p>
                    </a>
                </li>

                <!-- PROGRESS PROYEK -->
                <li class="nav-item">
                    <a href="{{ route('project.progress.pick') }}"
                        class="nav-link {{ request()->routeIs('project.progress.pick') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tasks"></i>
                        <p>Progress Proyek</p>
                    </a>
                    </li>


                <!-- PENGELUARAN PROYEK -->
                <li class="nav-item">
                    <a href="{{ route('project.expenses.pick') }}"
                        class="nav-link {{ request()->routeIs('project.expenses.pick') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>Pengeluaran Proyek</p>
                    </a>
                    </li>

                <!-- JADWAL -->
                <li class="nav-item">
                    <a href="{{ route('jadwal.index') }}"
                       class="nav-link {{ request()->is('jadwal*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>Jadwal Tahapan</p>
                    </a>
                </li>

                <!-- PEMINJAMAN ALAT -->
                <li class="nav-item">
                    <a href="{{ route('equipment_loans.index') }}"
                       class="nav-link {{ request()->is('equipment-loans*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-handshake"></i>
                        <p>Peminjaman Alat</p>
                    </a>
                </li>

                <!-- REPORT -->
                <li class="nav-item">
                    <a href="{{ route('report.pick') }}"
                        class="nav-link {{ request()->is('laporan*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-print"></i>
                        <p>Laporan</p>
                    </a>
                    </li>


            </ul>
        </nav>
    </div>
</aside>
