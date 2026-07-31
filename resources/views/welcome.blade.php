<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'Johen Sukses Abadi') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body { font-family: 'Inter', sans-serif; background: linear-gradient(135deg, #f8fafc 0%, #eef2ff 50%, #f5f3ff 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; color: #1e293b; }
            .container { text-align: center; padding: 2rem; max-width: 480px; }
            .logo { width: 64px; height: 64px; background: linear-gradient(135deg, #6366f1, #7c3aed); border-radius: 16px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; font-size: 1.5rem; font-weight: 800; color: white; box-shadow: 0 8px 32px rgba(99,102,241,0.3); }
            h1 { font-size: 2rem; font-weight: 800; margin-bottom: 0.5rem; background: linear-gradient(135deg, #4f46e5, #7c3aed); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
            p { color: #64748b; font-size: 0.95rem; line-height: 1.6; margin-bottom: 2rem; }
            .buttons { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }
            .btn-primary { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 2rem; background: linear-gradient(135deg, #6366f1, #7c3aed); color: white; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: all 0.2s; box-shadow: 0 4px 16px rgba(99,102,241,0.3); }
            .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 6px 24px rgba(99,102,241,0.4); }
            .btn-secondary { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.75rem 2rem; background: white; color: #475569; border-radius: 12px; text-decoration: none; font-weight: 600; font-size: 0.9rem; border: 1px solid #e2e8f0; transition: all 0.2s; }
            .btn-secondary:hover { background: #f8fafc; border-color: #cbd5e1; }
            .footer { margin-top: 3rem; font-size: 0.75rem; color: #94a3b8; }
        </style>
    </head>
    <body>
        <div class="container">
            <img src="{{ asset('logo.png') }}" alt="Johen Sukses Abadi" class="logo" style="width: 64px; height: 64px; object-fit: contain; border-radius: 16px; margin: 0 auto 1.5rem; display: block; box-shadow: 0 8px 32px rgba(99,102,241,0.3);">
            <h1>{{ config('app.name', 'Payroll') }}</h1>
            <p>Kelola payroll, generate slip gaji, dan kirim email ke karyawan dengan mudah dalam satu platform.</p>
            <div class="buttons">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-primary">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn-primary">Masuk</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn-secondary">Daftar</a>
                    @endif
                @endauth
            </div>
            <div class="footer">&copy; {{ date('Y') }} PT. Johen Sukses Abadi. All rights reserved.</div>
        </div>
    </body>
</html>
