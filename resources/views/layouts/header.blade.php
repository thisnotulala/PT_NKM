<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    {{-- LEFT --}}
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#">
                <i class="fas fa-bars"></i>
            </a>
        </li>
    </ul>

    {{-- RIGHT --}}
    <ul class="navbar-nav ml-auto">

        {{-- USER DROPDOWN --}}
        @auth
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-user-circle mr-1"></i>
                <span class="d-none d-md-inline">
                    {{ auth()->user()->name }}
                </span>
            </a>

            <div class="dropdown-menu dropdown-menu-right">
                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                    <i class="fas fa-user mr-2"></i> Profil Saya
                </a>

                <div class="dropdown-divider"></div>

                <form action="{{ route('logout') }}" method="POST" class="px-3">
                    @csrf
                    <button type="submit" class="btn btn-maroon btn-block btn-sm">
                        <i class="fas fa-sign-out-alt mr-1"></i> Logout
                    </button>
                </form>
            </div>
        </li>
        @endauth

    </ul>
</nav>
