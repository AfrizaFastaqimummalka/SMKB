<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Sistem Keuangan CV Panca Mitra Cendekia</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
            display: flex;
            background: #f0f4ff;
        }

        /* ── PANEL KIRI ── */
        .left-panel {
            width: 45%;
            min-height: 100vh;
            background: linear-gradient(160deg, #1a3fa8 0%, #1e4fcf 40%, #2563eb 70%, #3b82f6 100%);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 56px 52px;
            position: relative;
            overflow: hidden;
        }
        .left-panel::before {
            content: '';
            position: absolute;
            top: -120px; right: -80px;
            width: 380px; height: 380px;
            border-radius: 50%;
            background: rgba(255,255,255,0.07);
        }
        .left-panel::after {
            content: '';
            position: absolute;
            bottom: -100px; left: -60px;
            width: 280px; height: 280px;
            border-radius: 50%;
            background: rgba(255,255,255,0.05);
        }
        .logo-box {
            width: 52px; height: 52px;
            background: white;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 32px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }
        .logo-box svg { width: 28px; height: 28px; }
        .left-title {
            font-size: 28px;
            font-weight: 800;
            color: white;
            line-height: 1.25;
            margin-bottom: 14px;
        }
        .left-desc {
            font-size: 14px;
            color: rgba(255,255,255,0.78);
            line-height: 1.7;
            margin-bottom: 36px;
            max-width: 320px;
        }
        .feature-list { list-style: none; display: flex; flex-direction: column; gap: 12px; }
        .feature-list li {
            display: flex; align-items: center; gap: 10px;
            font-size: 13.5px; color: rgba(255,255,255,0.88);
        }
        .feature-list .chk {
            width: 20px; height: 20px; flex-shrink: 0;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }
        .feature-list .chk::after {
            content: '✓';
            font-size: 11px;
            color: white;
            font-weight: 700;
        }

        /* ── PANEL KANAN ── */
        .right-panel {
            flex: 1;
            min-height: 100vh;
            background: #eef3ff;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 32px;
            gap: 24px;
        }

        /* ── CARD LOGIN ── */
        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 4px 32px rgba(37, 99, 235, 0.10);
            padding: 40px 36px;
            width: 100%;
            max-width: 400px;
        }
        .card-icon {
            width: 48px; height: 48px;
            background: #eff6ff;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 20px;
        }
        .card-icon svg { width: 24px; height: 24px; }
        .card-title {
            font-size: 20px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }
        .card-sub {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 28px;
        }

        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }
        .form-input {
            width: 100%;
            padding: 10px 14px;
            border: 1.5px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #111827;
            background: #fff;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }
        .form-input:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
        }
        .form-input::placeholder { color: #9ca3af; }

        .pass-wrap { position: relative; }
        .pass-wrap .form-input { padding-right: 42px; }
        .pass-toggle {
            position: absolute; right: 13px; top: 50%; transform: translateY(-50%);
            background: none; border: none; cursor: pointer; padding: 0;
            color: #9ca3af; display: flex;
        }
        .pass-toggle:hover { color: #6b7280; }

        .btn-masuk {
            width: 100%;
            padding: 11px;
            background: #2563eb;
            color: white;
            font-size: 14px;
            font-weight: 700;
            font-family: 'Plus Jakarta Sans', sans-serif;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            transition: background .2s, transform .1s;
            margin-top: 6px;
        }
        .btn-masuk:hover { background: #1d4ed8; }
        .btn-masuk:active { transform: scale(.98); }

        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            border-radius: 9px;
            padding: 10px 14px;
            font-size: 13px;
            margin-bottom: 18px;
        }

        /* ── FOOTER ── */
        .right-footer {
            text-align: center;
            color: #6b7280;
            font-size: 12px;
            line-height: 1.7;
        }
        .footer-logos {
            display: flex; align-items: center; justify-content: center;
            gap: 16px; margin-top: 12px;
        }
        .footer-logo-badge {
            display: flex; align-items: center; gap: 6px;
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 6px 12px;
            font-size: 12px;
            font-weight: 700;
            color: #1a3fa8;
        }

        @media (max-width: 768px) {
            body { flex-direction: column; }
            .left-panel { width: 100%; min-height: auto; padding: 40px 28px; }
            .right-panel { padding: 32px 20px; }
            .login-card { padding: 28px 20px; }
        }
    </style>
</head>
<body>

    {{-- ═══ PANEL KIRI ═══ --}}
    <div class="left-panel">
        <div class="logo-box">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2L3 7l9 5 9-5-9-5z" fill="#2563eb"/>
                <path d="M3 12l9 5 9-5" stroke="#2563eb" stroke-width="2" stroke-linecap="round"/>
                <path d="M3 17l9 5 9-5" stroke="#93c5fd" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>

        <h1 class="left-title">CV Panca Mitra Cendekia<br>Sistem Keuangan</h1>
        <p class="left-desc">
            Kelola pemasukan, pengeluaran, dan laporan keuangan usaha Anda dengan mudah, terstruktur, dan didukung rekomendasi berbasis AI.
        </p>

        <ul class="feature-list">
            <li><span class="chk"></span>Laporan harian, bulanan &amp; tahunan</li>
            <li><span class="chk"></span>Export Excel otomatis</li>
            <li><span class="chk"></span>Rekomendasi kesehatan keuangan AI</li>
            <li><span class="chk"></span>Uji akurasi hasil rekomendasi</li>
        </ul>
    </div>

    {{-- ═══ PANEL KANAN ═══ --}}
    <div class="right-panel">
        <div class="login-card">
            <div class="card-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="11" width="18" height="11" rx="2"/>
                    <path d="M7 11V7a5 5 0 0110 0v4"/>
                </svg>
            </div>
            <h2 class="card-title">Masuk ke Sistem</h2>
            <p class="card-sub">Masukkan kredensial admin Anda</p>

            @if ($errors->any())
                <div class="alert-error">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label for="username" class="form-label">Username</label>
                    <input id="username" name="username" type="text"
                           value="{{ old('username') }}" required autofocus
                           placeholder="Masukkan username"
                           class="form-input">
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">Password</label>
                    <div class="pass-wrap">
                        <input id="password" name="password" type="password" required
                               placeholder="Masukkan password"
                               class="form-input">
                        <button type="button" class="pass-toggle" onclick="togglePass()" aria-label="Tampilkan password">
                            <svg id="eye-icon" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn-masuk">Masuk</button>
            </form>
        </div>

        <div class="right-footer">
            <p>Sistem Manajemen Keuangan</p>
            <p>CV Panca Mitra Cendekia &copy; {{ date('Y') }}</p>
            <div class="footer-logos">
                <div class="footer-logo-badge">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#2563eb"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2" stroke="white" stroke-width="2" stroke-linecap="round"/></svg>
                    PMC
                </div>
                <div class="footer-logo-badge">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                    SMKB
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePass() {
            const inp = document.getElementById('password');
            const ico = document.getElementById('eye-icon');
            if (inp.type === 'password') {
                inp.type = 'text';
                ico.innerHTML = '<path d="M17.94 17.94A10.07 10.07 0 0112 20c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>';
            } else {
                inp.type = 'password';
                ico.innerHTML = '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>';
            }
        }
    </script>
</body>
</html>
