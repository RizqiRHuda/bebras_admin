<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Bebras</title>
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: white;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
            max-width: 500px;
            margin: auto;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .btn-custom {
            background: #4f46e5;
            border: none;
            font-size: 1rem;
            font-weight: 600;
            transition: 0.3s ease-in-out;
        }

        .btn-custom:hover {
            background: #4338ca;
            transform: scale(1.02);
        }

        .form-check-input:checked {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }

        .logo-img img {
            max-width: 120px;
            border-radius: 5%;
        }

        @media (max-width: 768px) {
            .card {
                margin: 1rem;
                max-width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="d-flex align-items-center justify-content-center min-vh-100">
        <div class="card p-4 w-100">
            <div class="card-body">
                <!-- Logo -->
                <div class="logo-img text-center mb-3">
                    <img src="{{ asset('assets/img/logo/logo.jpg') }}" alt="Logo">
                </div>

                <h4 class="text-center fw-bold mb-3">Selamat Datang 👋</h4>
                <p class="text-center text-muted">Silakan login untuk melanjutkan</p>

                <!-- Form -->
                <form action="{{ route('login.post') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" id="username"
                            placeholder="Masukkan username">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" id="password"
                            placeholder="Masukkan password" required>
                    </div>

                    <button type="submit" class="btn btn-custom w-100 py-2 rounded-3 text-white">Sign In</button>
                </form>

                @if ($errors->any())
                    <div class="alert alert-danger mt-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="text-center mt-4">
                    <p class="mb-0">Belum punya akun?
                        <a href="#" class="fw-semibold text-decoration-none text-primary">Daftar</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
