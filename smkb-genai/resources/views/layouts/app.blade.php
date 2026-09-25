<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard') — Sistem Keuangan Berbasis AI</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ['Fraunces', 'serif'],
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        navy: { 950: '#0B1D3A', 800: '#142E5C' },
                        brand: { 600: '#2563EB', 400: '#60A5FA', 100: '#DBEAFE', 50: '#EFF6FF' },
                    },
                },
            },
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .font-display { font-family: 'Fraunces', serif; }
        .sidebar-gradient { background: linear-gradient(180deg, #0B1D3A 0%, #142E5C 55%, #2563EB 130%); }
        .header-wash { background: linear-gradient(120deg, #EFF6FF 0%, #FFFFFF 55%, #EFF6FF 100%); }
    </style>
</head>
<body class="bg-white text-slate-900 antialiased">
    <div class="min-h-screen flex">
        <aside class="hidden md:flex md:w-60 md:flex-col sidebar-gradient text-white">
            <div class="px-6 py-6 border-b border-white/10">
                <p class="font-display text-lg leading-tight">Sistem Keuangan</p>
                <p class="text-xs text-blue-200/80 mt-0.5">Berbasis Generative AI</p>
            </div>
            <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
                @php
                    $navItems = [
                        ['route' => 'dashboard', 'label' => 'Dashboard'],
                        ['route' => 'pemasukan.index', 'label' => 'Pemasukan'],
                        ['route' => 'pengeluaran.index', 'label' => 'Pengeluaran'],
                        ['route' => 'rekomendasi.index', 'label' => 'Rekomendasi Kesehatan'],
                        ['route' => 'uji-akurasi.index', 'label' => 'Uji Akurasi'],
                        ['route' => 'laporan.index', 'label' => 'Laporan'],
                    ];
                @endphp
                @foreach ($navItems as $item)
                    <a href="{{ route($item['route']) }}"
                       class="block px-3 py-2 rounded-lg transition-colors {{ request()->routeIs($item['route']) || request()->routeIs(str($item['route'])->before('.').'.*') ? 'bg-white/15 font-medium' : 'text-blue-100/85 hover:bg-white/10' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>
            <div class="px-6 py-4 border-t border-white/10 text-xs text-blue-200/70">
                Rekomendasi kesehatan keuangan dihasilkan otomatis oleh AI (Gemini).
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-w-0">
            <header class="header-wash border-b border-blue-100">
                <div class="px-4 md:px-8 py-4 flex items-center justify-between">
                    <div>
                        <h1 class="font-display text-xl text-navy-950">@yield('title', 'Dashboard')</h1>
                        @hasSection('subtitle')
                            <p class="text-sm text-slate-500 mt-0.5">@yield('subtitle')</p>
                        @endif
                    </div>
                    @auth
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-sm text-slate-500 hover:text-navy-950">
                                Keluar ({{ auth()->user()->username }})
                            </button>
                        </form>
                    @endauth
                </div>
            </header>

            <main class="flex-1 px-4 md:px-8 py-6">
                @if (session('status'))
                    <div class="mb-5 rounded-lg border border-green-200 bg-green-50 text-green-800 text-sm px-4 py-3">
                        {{ session('status') }}
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 text-red-800 text-sm px-4 py-3">
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
