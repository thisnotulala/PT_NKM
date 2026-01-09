<aside class="main-sidebar elevation-4">
    <a href="#" class="brand-link">
        Nusantara Klik Makmur
    </a>

    <div class="sidebar">
        <nav>
            <ul class="nav nav-pills nav-sidebar flex-column">

                @php
                    $role = auth()->user()->role;
                @endphp

                <!-- DASHBOARD (SEMUA ROLE) -->
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}"
                       class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-chart-line"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <!-- MASTER DATA -->
                <li class="nav-header">MASTER DATA</li>

                <!-- MANAJEMEN USER (SITE MANAGER SAJA) -->
                @if($role === 'site manager')
                <li class="nav-item">
                    <a href="{{ route('user.index') }}"
                       class="nav-link {{ request()->is('user*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Manajemen User</p>
                    </a>
                </li>
                @endif

                <!-- CLIENT (SITE MANAGER & ADMINISTRASI) -->
                @if(in_array($role, ['site manager','administrasi']))
                <li class="nav-item">
                    <a href="{{ route('client.index') }}"
                       class="nav-link {{ request()->is('client*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-building"></i>
                        <p>Client</p>
                    </a>
                </li>
                @endif

                <!-- SDM (SEMUA ROLE) -->
                <li class="nav-item">
                    <a href="{{ route('sdm.index') }}"
                       class="nav-link {{ request()->is('sdm*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-users"></i>
                        <p>SDM</p>
                    </a>
                </li>

                <!-- SATUAN (SITE MANAGER & ADMINISTRASI) -->
                @if(in_array($role, ['site manager','administrasi']))
                <li class="nav-item">
                    <a href="{{ route('satuan.index') }}"
                       class="nav-link {{ request()->is('satuan*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-ruler-combined"></i>
                        <p>Satuan</p>
                    </a>
                </li>
                @endif

                <!-- EQUIPMENT (SEMUA ROLE) -->
                @if(in_array($role, ['site manager','administrasi','kepala lapangan']))
                <li class="nav-item">
                    <a href="{{ route('equipment.index') }}"
                       class="nav-link {{ request()->is('equipment*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-toolbox"></i>
                        <p>Equipment</p>
                    </a>
                </li>
                @endif

                <!-- PROYEK -->
                <li class="nav-header">PROYEK</li>

                <!-- PROYEK (SEMUA ROLE) -->
                <li class="nav-item">
                    <a href="{{ route('project.index') }}"
                       class="nav-link {{ request()->is('project*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-project-diagram"></i>
                        <p>Proyek</p>
                    </a>
                </li>

                <!-- PROGRESS PROYEK (SEMUA ROLE) -->
                <li class="nav-item">
                    <a href="{{ route('project.progress.pick') }}"
                       class="nav-link {{ request()->routeIs('project.progress.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tasks"></i>
                        <p>Progress Proyek</p>
                    </a>
                </li>

                <!-- MATERIAL PROYEK (SITE MANAGER & ADMINISTRASI) -->
                @if(in_array($role, ['site manager','administrasi']))
                <li class="nav-item">
                    <a href="{{ route('project.materials.pick') }}"
                    class="nav-link {{ request()->routeIs('project.materials.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-boxes"></i>
                        <p>Material Proyek</p>
                    </a>
                </li>
                @endif

                <!-- PENGELUARAN PROYEK (❗SITE MANAGER & ADMINISTRASI SAJA) -->
                @if(in_array($role, ['site manager','administrasi']))
                <li class="nav-item">
                    <a href="{{ route('project.expenses.pick') }}"
                       class="nav-link {{ request()->routeIs('project.expenses.*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-money-bill-wave"></i>
                        <p>Pengeluaran Proyek</p>
                    </a>
                </li>
                @endif

                <!-- JADWAL TAHAPAN (SEMUA ROLE) -->
                <li class="nav-item">
                    <a href="{{ route('jadwal.index') }}"
                       class="nav-link {{ request()->is('jadwal*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-calendar-alt"></i>
                        <p>Jadwal Tahapan</p>
                    </a>
                </li>

                <!-- PEMINJAMAN ALAT (SEMUA ROLE) -->
                <li class="nav-item">
                    <a href="{{ route('equipment_loans.index') }}"
                       class="nav-link {{ request()->is('equipment-loans*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-handshake"></i>
                        <p>Peminjaman Alat</p>
                    </a>
                </li>

                <!-- LAPORAN (SITE MANAGER & ADMINISTRASI) -->
                @if(in_array($role, ['site manager','administrasi']))
                <li class="nav-item">
                    <a href="{{ route('report.pick') }}"
                       class="nav-link {{ request()->is('laporan*') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-print"></i>
                        <p>Laporan</p>
                    </a>
                </li>
                @endif

            </ul>
        </nav>
    </div>
</aside>
