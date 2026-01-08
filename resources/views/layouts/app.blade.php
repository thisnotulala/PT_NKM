<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>@yield('title', 'SIP Proyek')</title>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/css/adminlte.min.css') }}">

    <style>
        :root {
            --maroon: #8B1E1E;
            --dark: #2F2F2F;
            --soft-bg: #F5F6FA;
        }

        body {
            background: var(--soft-bg);
            font-family: 'Inter', 'Segoe UI', sans-serif;
        }

        /* HEADER */
        .main-header {
            background: #fff;
            border-bottom: 2px solid var(--maroon);
        }

        .main-header .nav-link {
            color: #444;
        }

        /* SIDEBAR */
        .main-sidebar {
            background: linear-gradient(180deg, #2f2f2f, #1f1f1f);
        }

        .brand-link {
            background: transparent;
            border-bottom: 1px solid rgba(255,255,255,.1);
            text-align: center;
            font-weight: 600;
            color: #fff !important;
            letter-spacing: 1px;
        }

        .nav-sidebar .nav-link {
            color: #cfcfcf;
            margin: 4px 8px;
            border-radius: 8px;
        }

        .nav-sidebar .nav-link.active,
        .nav-sidebar .nav-link:hover {
            background: var(--maroon);
            color: #fff;
        }

        /* CONTENT */
        .content-wrapper {
            background: var(--soft-bg);
            padding: 25px;
        }

        /* CARD */
        .card {
            border-radius: 14px;
            border: none;
            box-shadow: 0 10px 25px rgba(0,0,0,.06);
        }

        .card-header {
            background: transparent;
            border-bottom: none;
            font-weight: 600;
        }

        /* DASHBOARD CARD */
        .stat-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stat-card i {
            font-size: 32px;
            color: var(--maroon);
        }

        /* BUTTON */
        .btn-maroon {
            background: var(--maroon);
            color: #fff;
            border-radius: 10px;
            padding: 8px 16px;
        }

        .btn-maroon:hover {
            background: #6e1717;
            color: #fff;
        }
        /* SIDEBAR HEADER (MASTER DATA) */
        .nav-header {
            color: #ffffff !important;
            font-size: 11px;
            letter-spacing: 1px;
            margin: 15px 10px 5px;
            opacity: 0.8;
        }

    /* AKSI BUTTON */
    .btn-action {
        border-radius: 8px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 500;
        transition: .2s ease;
    }

    .btn-edit {
        color: var(--maroon);
        border: 1px solid var(--maroon);
        background: transparent;
    }

    .btn-edit:hover {
        background: var(--maroon);
        color: #fff;
    }

    .btn-delete {
        background: var(--maroon);
        color: #fff;
        border: none;
    }

    .btn-delete:hover {
        background: #6e1717;
        color: #fff;
    }
    /* WRAPPER AKSI */
    .action-group {
        display: flex;
        justify-content: center;
        gap: 8px; /* jarak antar tombol */
    }

    /* BUTTON AKSI SERAGAM */
    .btn-action {
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        font-size: 13px;
        transition: .2s ease;
    }

    /* EDIT */
    .btn-edit {
        color: var(--maroon);
        border: 1px solid var(--maroon);
        background: #fff;
    }

    .btn-edit:hover {
        background: var(--maroon);
        color: #fff;
    }

    /* DELETE */
    .btn-delete {
        background: var(--maroon);
        color: #fff;
        border: none;
    }

    .btn-delete:hover {
        background: #6e1717;
    }

    </style>
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

    @include('layouts.header')
    @include('layouts.sidebar')

    <div class="content-wrapper">
        @yield('content')
    </div>

</div>

<script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('adminlte/js/adminlte.min.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

</body>
</html>
