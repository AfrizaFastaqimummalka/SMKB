<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — CV Panca Mitra Cendekia</title>
    <meta name="description" content="Sistem Manajemen Keuangan CV Panca Mitra Cendekia berbasis Generative AI">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --blue-50:  #eff6ff;
            --blue-100: #dbeafe;
            --blue-200: #bfdbfe;
            --blue-500: #3b82f6;
            --blue-600: #2563eb;
            --blue-700: #1d4ed8;
            --blue-800: #1e40af;
            --blue-900: #1e3a8a;
            --sidebar-w: 210px;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: #f5f7fb;
            color: #111827;
            min-height: 100vh;
            display: flex;
        }

        /* ══════════ SIDEBAR ══════════ */
        .sidebar {
            width: var(--sidebar-w);
            min-height: 100vh;
            background: white;
            border-right: 1px solid #e5e7eb;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 50;
        }

        .sidebar-brand {
            padding: 20px 20px 16px;
            border-bottom: 1px solid #f3f4f6;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .brand-icon {
            width: 36px; height: 36px;
            background: var(--blue-600);
            border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .brand-icon svg { width: 20px; height: 20px; }
        .brand-text { min-width: 0; }
        .brand-name {
            font-size: 13px;
            font-weight: 800;
            color: #111827;
            line-height: 1.2;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .brand-role {
            font-size: 11px;
            color: #6b7280;
            margin-top: 1px;
        }

        .sidebar-section-label {
            padding: 16px 20px 6px;
            font-size: 10.5px;
            font-weight: 700;
            color: #9ca3af;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .sidebar-nav { padding: 4px 10px; flex: 1; }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 9px;
            font-size: 13.5px;
            font-weight: 500;
            color: #374151;
            text-decoration: none;
            margin-bottom: 2px;
            transition: background .15s, color .15s;
        }
        .nav-item:hover { background: var(--blue-50); color: var(--blue-700); }
        .nav-item.active {
            background: var(--blue-50);
            color: var(--blue-600);
            font-weight: 700;
        }
        .nav-item.active .nav-icon { color: var(--blue-600); }
        .nav-icon {
            width: 18px; height: 18px;
            flex-shrink: 0;
            color: #9ca3af;
        }
        .nav-item:hover .nav-icon { color: var(--blue-600); }

        .sidebar-footer {
            padding: 14px 16px;
            border-top: 1px solid #f3f4f6;
        }
        .sidebar-user {
            font-size: 11.5px;
            color: #6b7280;
            margin-bottom: 8px;
            line-height: 1.4;
        }
        .sidebar-user strong { color: #374151; font-weight: 700; }
        .btn-logout {
            display: flex;
            align-items: center;
            gap: 7px;
            width: 100%;
            padding: 8px 12px;
            border: 1.5px solid #e5e7eb;
            border-radius: 8px;
            background: white;
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            cursor: pointer;
            transition: border-color .15s, background .15s;
            text-decoration: none;
        }
        .btn-logout:hover { border-color: var(--blue-300, #93c5fd); background: var(--blue-50); color: var(--blue-700); }

        /* ══════════ MAIN ══════════ */
        .main-wrap {
            margin-left: var(--sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            min-width: 0;
        }

        .topbar {
            background: white;
            border-bottom: 1px solid #e5e7eb;
            padding: 0 28px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 40;
        }
        .topbar-title h1 {
            font-size: 18px;
            font-weight: 800;
            color: #111827;
        }
        .topbar-title p {
            font-size: 12px;
            color: #6b7280;
            margin-top: 1px;
        }
        .topbar-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* ══════════ CONTENT ══════════ */
        .page-content {
            padding: 24px 28px;
            flex: 1;
        }

        /* ══════════ ALERTS ══════════ */
        .alert {
            border-radius: 10px;
            padding: 11px 16px;
            font-size: 13.5px;
            margin-bottom: 20px;
            display: flex;
            align-items: flex-start;
            gap: 10px;
        }
        .alert-success {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #15803d;
        }
        .alert-error {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
        }

        /* ══════════ CARDS ══════════ */
        .card {
            background: white;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            padding: 20px 22px;
        }
        .card-sm { padding: 16px 18px; }

        /* ══════════ TABLES ══════════ */
        .table-wrap {
            background: white;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            overflow: hidden;
        }
        table { width: 100%; border-collapse: collapse; }
        thead th {
            background: #f9fafb;
            padding: 11px 16px;
            font-size: 11px;
            font-weight: 700;
            color: #6b7280;
            text-align: left;
            letter-spacing: .05em;
            text-transform: uppercase;
            border-bottom: 1px solid #e5e7eb;
        }
        tbody tr { transition: background .1s; }
        tbody tr:hover { background: #f9fafb; }
        tbody td {
            padding: 12px 16px;
            font-size: 13.5px;
            color: #374151;
            border-bottom: 1px solid #f3f4f6;
        }
        tbody tr:last-child td { border-bottom: none; }

        /* ══════════ BUTTONS ══════════ */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 9px;
            font-size: 13.5px;
            font-weight: 600;
            font-family: 'Plus Jakarta Sans', sans-serif;
            cursor: pointer;
            text-decoration: none;
            border: none;
            transition: background .15s, transform .1s;
        }
        .btn:active { transform: scale(.98); }
        .btn-primary { background: var(--blue-600); color: white; }
        .btn-primary:hover { background: var(--blue-700); }
        .btn-outline {
            background: white;
            color: #374151;
            border: 1.5px solid #e5e7eb;
        }
        .btn-outline:hover { border-color: var(--blue-300, #93c5fd); background: var(--blue-50); }
        .btn-danger { background: #fef2f2; color: #dc2626; border: 1.5px solid #fecaca; }
        .btn-danger:hover { background: #fee2e2; }
        .btn-sm { padding: 6px 12px; font-size: 12.5px; }

        /* ══════════ BADGES ══════════ */
        .badge {
            display: inline-flex;
            align-items: center;
            padding: 3px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 600;
        }
        .badge-blue   { background: #eff6ff; color: #2563eb; }
        .badge-green  { background: #f0fdf4; color: #15803d; }
        .badge-red    { background: #fef2f2; color: #dc2626; }
        .badge-amber  { background: #fffbeb; color: #b45309; }

        /* ══════════ EMPTY STATE ══════════ */
        .empty-state {
            text-align: center;
            padding: 56px 16px;
        }
        .empty-icon {
            width: 52px; height: 52px;
            background: #f3f4f6;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 16px;
        }
        .empty-icon svg { width: 26px; height: 26px; color: #9ca3af; }
        .empty-state h3 {
            font-size: 15px;
            font-weight: 700;
            color: #374151;
            margin-bottom: 6px;
        }
        .empty-state p {
            font-size: 13px;
            color: #9ca3af;
            margin-bottom: 20px;
        }

        /* ══════════ FORM ══════════ */
        .form-group { margin-bottom: 18px; }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            color: #374151;
            margin-bottom: 6px;
        }
        .form-input, .form-select {
            width: 100%;
            padding: 9px 13px;
            border: 1.5px solid #e5e7eb;
            border-radius: 9px;
            font-size: 13.5px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #111827;
            background: white;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-input:focus, .form-select:focus {
            border-color: var(--blue-600);
            box-shadow: 0 0 0 3px rgba(37,99,235,.1);
        }
        .form-input::placeholder { color: #9ca3af; }
        .form-select { appearance: none; cursor: pointer; }

        /* ══════════ STAT CARD ══════════ */
        .stat-card {
            background: white;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 18px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .stat-label {
            font-size: 11px;
            font-weight: 700;
            color: #9ca3af;
            letter-spacing: .05em;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .stat-value {
            font-size: 22px;
            font-weight: 800;
            line-height: 1;
        }
        .stat-icon {
            width: 44px; height: 44px;
            border-radius: 11px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .stat-icon svg { width: 22px; height: 22px; }
        .stat-icon-green { background: #f0fdf4; color: #16a34a; }
        .stat-icon-red   { background: #fef2f2; color: #dc2626; }
        .stat-icon-blue  { background: #eff6ff; color: #2563eb; }

        /* ══════════ RESPONSIVE ══════════ */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .main-wrap { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>

    {{-- ═══ SIDEBAR ═══ --}}
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon" style="background: transparent;">
                <img src="{{ asset('logo.png') }}" alt="Logo" style="width: 100%; height: 100%; object-fit: contain;">
            </div>
            <div class="brand-text">
                <div class="brand-name">CV Panca Mitra<br>Cendekia</div>
                <div class="brand-role">Admin Panel</div>
            </div>
        </div>

        <div class="sidebar-section-label">Menu Utama</div>
        <nav class="sidebar-nav">
            @php
                $navItems = [
                    ['route' => 'dashboard',          'label' => 'Dashboard',      'icon' => '<path d="M3 3h7v7H3zM14 3h7v7h-7zM14 14h7v7h-7zM3 14h7v7H3z"/>'],
                    ['route' => 'pemasukan.index',    'label' => 'Pemasukan',      'icon' => '<line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>'],
                    ['route' => 'pengeluaran.index',  'label' => 'Pengeluaran',    'icon' => '<line x1="5" y1="12" x2="19" y2="12"/>'],
                    ['route' => 'rekomendasi.index',  'label' => 'Rekomendasi AI', 'icon' => '<circle cx="12" cy="12" r="10"/><path d="M12 8v4l3 3"/>'],
                    ['route' => 'uji-akurasi.index',  'label' => 'Uji Akurasi',   'icon' => '<polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>'],
                    ['route' => 'laporan.index',      'label' => 'Laporan',        'icon' => '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>'],
                ];
            @endphp

            @foreach ($navItems as $item)
                @php
                    $base = str($item['route'])->before('.');
                    $isActive = request()->routeIs($item['route'])
                        || request()->routeIs($base . '.*');
                @endphp
                <a href="{{ route($item['route']) }}"
                   class="nav-item {{ $isActive ? 'active' : '' }}">
                    <svg class="nav-icon" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        {!! $item['icon'] !!}
                    </svg>
                    {{ $item['label'] }}
                </a>
            @endforeach
        </nav>

        @auth
        <div class="sidebar-footer">
            <div class="sidebar-user">
                Login sebagai: <strong>{{ auth()->user()->name ?? auth()->user()->username }}</strong>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                        <polyline points="16 17 21 12 16 7"/>
                        <line x1="21" y1="12" x2="9" y2="12"/>
                    </svg>
                    Keluar
                </button>
            </form>
        </div>
        @endauth
    </aside>

    {{-- ═══ MAIN WRAP ═══ --}}
    <div class="main-wrap">
        <header class="topbar">
            <div class="topbar-title">
                <h1>@yield('title', 'Dashboard')</h1>
                @hasSection('subtitle')
                    <p>@yield('subtitle')</p>
                @endif
            </div>
            <div class="topbar-right">
                @yield('topbar-actions')
            </div>
        </header>

        <main class="page-content">
            @if (session('status'))
                <div class="alert alert-success">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ session('status') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="alert alert-error">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <div>
                        @foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
