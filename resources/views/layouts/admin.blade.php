<!DOCTYPE html>
<html>
<head>
    <title>Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <style>
        body { margin:0 }
        .sidebar {
            width: 220px;
            min-height: 100vh;
            background: #343a40;
        }
        .sidebar a {
            color: #fff;
            text-decoration: none;
            display: block;
            padding: 10px;
        }
        .sidebar a:hover {
            background: #495057;
        }
    </style>
</head>
<body>

<div class="d-flex">
    @include('partials.sidebar')

    <div class="flex-grow-1">
        @include('partials.header')

        <div class="p-4">
            @yield('content')
        </div>
    </div>
</div>

</body>
</html>
