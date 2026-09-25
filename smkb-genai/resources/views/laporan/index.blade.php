@extends('layouts.app')

@section('title', 'Laporan Keuangan')
@section('subtitle', $dari->format('d M Y') . ' — ' . $sampai->format('d M Y'))

@section('content')
    <div class="flex items-center justify-between mb-6 gap-3 flex-wrap">
        <form method="GET" class="flex flex-wrap gap-2 text-sm">
            @foreach (['harian' => 'Harian', 'bulan' => 'Bulanan', 'tahunan' => 'Tahunan'] as $key => $label)
                <a href="{{ route('laporan.index', ['periode' => $key]) }}"
                   class="px-3 py-1.5 rounded-full border {{ $periode === $key ? 'bg-navy-950 text-white border-navy-950' : 'border-slate-300 text-slate-600 hover:border-navy-950' }}">
                    {{ $label }}
                </a>
            @endforeach
        </form>
        <a href="{{ route('laporan.export', request()->query()) }}"
           class="rounded-lg bg-navy-950 text-white text-sm font-medium px-4 py-2 hover:bg-navy-800">
            Export Excel
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="rounded-2xl border border-blue-100 bg-brand-50 p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500">Total Pemasukan</p>
            <p class="font-display text-2xl text-green-700 mt-1">Rp {{ number_format($ringkasan['pemasukan'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-blue-100 bg-brand-50 p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500">Total Pengeluaran</p>
            <p class="font-display text-2xl text-red-700 mt-1">Rp {{ number_format($ringkasan['pengeluaran'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-blue-100 bg-brand-50 p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500">Saldo</p>
            <p class="font-display text-2xl text-navy-950 mt-1">Rp {{ number_format($ringkasan['saldo'], 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="rounded-2xl border border-blue-100 bg-brand-50 p-4 mb-8 flex items-center justify-between">
        <p class="text-sm text-slate-600">Mau lihat penilaian kesehatan keuangan dari AI untuk periode ini?</p>
        <a href="{{ route('rekomendasi.index') }}" class="text-sm text-brand-600 font-medium hover:underline whitespace-nowrap">Buka Rekomendasi Kesehatan →</a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div>
            <h2 class="font-display text-lg text-navy-950 mb-3">Pemasukan</h2>
            <div class="rounded-2xl border border-blue-100 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-brand-50 text-slate-500 text-left">
                        <tr>
                            <th class="px-3 py-2 font-medium">Tanggal</th>
                            <th class="px-3 py-2 font-medium">Keterangan</th>
                            <th class="px-3 py-2 font-medium text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pemasukan as $p)
                            <tr>
                                <td class="px-3 py-2 text-slate-600">{{ $p->tanggal->format('d/m/Y') }}</td>
                                <td class="px-3 py-2 text-slate-700">{{ $p->keterangan ?? str($p->kategori)->headline() }}</td>
                                <td class="px-3 py-2 text-right text-green-700">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-3 py-6 text-center text-slate-400">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div>
            <h2 class="font-display text-lg text-navy-950 mb-3">Pengeluaran</h2>
            <div class="rounded-2xl border border-blue-100 overflow-hidden">
                <table class="w-full text-sm">
                    <thead class="bg-brand-50 text-slate-500 text-left">
                        <tr>
                            <th class="px-3 py-2 font-medium">Tanggal</th>
                            <th class="px-3 py-2 font-medium">Keterangan</th>
                            <th class="px-3 py-2 font-medium text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($pengeluaran as $p)
                            <tr>
                                <td class="px-3 py-2 text-slate-600">{{ $p->tanggal->format('d/m/Y') }}</td>
                                <td class="px-3 py-2 text-slate-700">{{ $p->keterangan ?? str($p->kategori)->headline() }}</td>
                                <td class="px-3 py-2 text-right text-red-700">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-3 py-6 text-center text-slate-400">Tidak ada data.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
