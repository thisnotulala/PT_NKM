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

                <!-- Satuan -->
                <li class="nav-item">
                <a href="{{ route('satuan.index') }}"
                    class="nav-link {{ request()->is('satuan*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-ruler-combined"></i>
                    <p>Satuan</p>
                </a>
                </li>

                <li class="nav-item">
                <a href="{{ route('equipment.index') }}"
                    class="nav-link {{ request()->is('equipment*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-toolbox"></i>
                    <p>Equipment</p>
                </a>
                </li>
                <li class="nav-item">
                <a href="{{ route('project.index') }}"
                    class="nav-link {{ request()->is('project*') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-project-diagram"></i>
                    <p>Proyek</p>
                </a>
                </li>

            </ul>
        </nav>
    </div>
</aside>

