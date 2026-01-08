<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'SIP Proyek')</title>

    {{-- Select2 CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    {{-- AdminLTE --}}
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
        .main-header .nav-link { color: #444; }

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

        /* SIDEBAR HEADER (MASTER DATA) */
        .nav-header {
            color: #ffffff !important;
            font-size: 11px;
            letter-spacing: 1px;
            margin: 15px 10px 5px;
            opacity: 0.8;
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

        /* AKSI BUTTON */
        .action-group {
            display: flex;
            justify-content: center;
            gap: 8px;
        }

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

        .btn-edit {
            color: var(--maroon);
            border: 1px solid var(--maroon);
            background: #fff;
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
        .btn-delete:hover { background: #6e1717; }

        /* Select2 biar rapi */
        .select2-container { width: 100% !important; }
        .select2-container .select2-selection--multiple{
            min-height: 38px;
            border: 1px solid #ced4da;
        }
        .select2-container--default .select2-selection--multiple .select2-selection__choice{
            margin-top: 6px;
        }
    </style>

    {{-- tambahan CSS per halaman --}}
    @stack('styles')
</head>

<body class="hold-transition sidebar-mini">
<div class="wrapper">

    {{-- Header & Sidebar --}}
    @include('layouts.header')
    @include('layouts.sidebar')

    {{-- Content --}}
    <div class="content-wrapper">
        @yield('content')
    </div>

</div>

{{-- AdminLTE JS (jQuery dari AdminLTE, JANGAN dobel) --}}
<script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('adminlte/js/adminlte.min.js') }}"></script>

{{-- Select2 JS --}}
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

{{-- tambahan JS per halaman --}}
@stack('scripts')
</body>
</html>
