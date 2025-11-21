<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #121212;
            color: #ffffff; /* default text white */
        }
    
        .card {
            background-color: #1e1e1e;
            border: none;
            border-radius: 10px;
            color: #ffffff; /* text inside card white */
        }
    
        h3, label, small, p, a {
            color: #ffffff; /* all headings, labels, small text, links default white */
        }
    
        .form-control {
            background-color: #2a2a2a;
            color: #ffffff; /* text inside inputs white */
            border: 1px solid #444;
        }
    
        .form-control::placeholder {
            color: #ccc; /* placeholder slightly lighter */
        }
    
        .form-control:focus {
            background-color: #2a2a2a;
            color: #ffffff;
            border-color: #007bff;
            box-shadow: none;
        }
    
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
    
        .btn-primary:hover {
            background-color: #0056b3;
        }
    
        a {
            color: #0d6efd;
            text-decoration: none;
        }
    
        a:hover {
            text-decoration: underline;
            color: #0a58ca;
        }
    </style>
</head>

<body>
    <div class="container vh-100 d-flex justify-content-center align-items-center">
        <div class="col-md-4">
            <div class="card p-4 shadow-lg">
                <h3 class="text-center mb-4">Login</h3>
                <form method="POST" action="{{route('admin.login_check')}}">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email address</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter email" value="{{ old('email') }}"> @error('email')
                        <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password"> @error('password')
                        <small class="text-danger">{{ $message }}</small> @enderror
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember">
                        <label class="form-check-label" for="remember">Remember me</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Login</button>
                </form>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>