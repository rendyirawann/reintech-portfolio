<!DOCTYPE html>
<html lang="en" data-dev-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Developer Login</title>
    @vite(['resources/css/developer.css', 'resources/js/developer.js'])
    <style>
        body { display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .login-card { width: 100%; max-width: 400px; padding: 2rem; }
        .form-group { margin-bottom: 1rem; }
        label { display: block; margin-bottom: 0.5rem; color: var(--dev-text-muted); font-size: 0.875rem; }
        .alert-danger { background-color: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2); padding: 1rem; border-radius: 0.375rem; margin-bottom: 1.5rem; }
    </style>
</head>
<body>
    <div class="dev-card login-card">
        <div style="text-align: center; margin-bottom: 2rem;">
            <h1 style="font-size: 1.5rem; font-weight: bold; margin-bottom: 0.5rem;">Developer Access</h1>
            <p style="color: var(--dev-text-muted);">Restricted Area</p>
        </div>

        @if($errors->any())
            <div class="alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('developer.login.post') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" class="dev-input" value="{{ old('email') }}" required autofocus>
            </div>
            
            <div class="form-group" style="margin-bottom: 1.5rem;">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="dev-input" required>
            </div>

            <button type="submit" class="dev-btn" style="width: 100%;">Authenticate</button>
        </form>
    </div>
</body>
</html>
