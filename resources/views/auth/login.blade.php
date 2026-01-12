<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Login | SIP Proyek</title>

    <link rel="stylesheet" href="{{ asset('adminlte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('adminlte/css/adminlte.min.css') }}">

    <style>
        body {
            height: 100vh;
            background: linear-gradient(135deg, #8B1E1E, #3a0f0f);
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Inter', 'Segoe UI', sans-serif;
        }

        .login-box {
            width: 420px;
        }

        .login-card {
            border-radius: 18px;
            box-shadow: 0 15px 40px rgba(0,0,0,.25);
            overflow: hidden;
        }

        .login-header {
            background: #8B1E1E;
            color: #fff;
            padding: 30px;
            text-align: center;
        }

        .login-header h2 {
            margin: 0;
            font-weight: 600;
            letter-spacing: 1px;
        }

        .login-body {
            background: #fff;
            padding: 35px;
        }

        .form-control {
            border-radius: 10px;
            height: 45px;
        }

        .btn-login {
            background: #8B1E1E;
            color: #fff;
            border-radius: 10px;
            height: 45px;
            font-weight: 600;
        }

        .btn-login:hover {
            background: #6e1717;
            color: #fff;
        }

        .login-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: #777;
        }
    </style>
</head>

<body>

<div class="login-box">
    <div class="card login-card">

        <div class="login-header">
            <h2>SIP PROYEK</h2>
            <small>Sistem Informasi Proyek</small>
        </div>

        <div class="login-body">

            {{-- ✅ Pesan jika email/password salah --}}
            @if ($errors->has('login_error'))
                <div class="alert alert-danger text-center">
                    {{ $errors->first('login_error') }}
                </div>
            @endif

            <form action="{{ url('/login') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control"
                           value="{{ old('email') }}" required autofocus>

                    {{-- (Opsional) error validasi email --}}
                    @error('email')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <div class="form-group mt-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>

                    {{-- (Opsional) error validasi password --}}
                    @error('password')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <button class="btn btn-login btn-block mt-4" type="submit">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
            </form>

            <div class="login-footer">
                © {{ date('Y') }} SIP Proyek
            </div>
        </div>

    </div>
</div>

</body>
</html>
