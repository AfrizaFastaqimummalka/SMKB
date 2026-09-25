<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Sistem Keuangan Buku</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,600&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { display: ['Fraunces', 'serif'], sans: ['Inter', 'sans-serif'] },
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
        .auth-gradient { background: linear-gradient(150deg, #0B1D3A 0%, #142E5C 45%, #2563EB 140%); }
    </style>
</head>
<body class="min-h-screen auth-gradient flex items-center justify-center px-4">
    <div class="w-full max-w-sm">
        <div class="text-center mb-6">
            <p class="font-display text-2xl text-white">Perusahaan Buku</p>
            <p class="text-sm text-blue-200/80 mt-1">Sistem Informasi Keuangan</p>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-6">
            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 text-red-700 text-sm px-3 py-2">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label for="username" class="block text-sm font-medium text-slate-700 mb-1">Username</label>
                    <input id="username" name="username" type="text" value="{{ old('username') }}" required autofocus
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-600 focus:border-brand-600">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700 mb-1">Password</label>
                    <input id="password" name="password" type="password" required
                           class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-600 focus:border-brand-600">
                </div>
                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" class="rounded border-slate-300">
                    Ingat saya
                </label>
                <button type="submit"
                        class="w-full rounded-lg bg-navy-950 text-white text-sm font-medium py-2.5 hover:bg-navy-800 transition-colors">
                    Masuk
                </button>
            </form>
        </div>
    </div>
</body>
</html>
