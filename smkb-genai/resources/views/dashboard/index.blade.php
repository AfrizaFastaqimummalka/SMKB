@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', 'Ringkasan keuangan dan aktivitas terbaru')

@section('content')
    @if ($rekomendasiTerbaru)
        @php
            $warnaLabel = match($rekomendasiTerbaru->label) {
                'sehat' => 'bg-green-50 text-green-700 border-green-200',
                'perlu_perhatian' => 'bg-amber-50 text-amber-700 border-amber-200',
                default => 'bg-red-50 text-red-700 border-red-200',
            };
        @endphp
        <a href="{{ route('rekomendasi.show', $rekomendasiTerbaru) }}"
           class="block rounded-2xl border {{ $warnaLabel }} p-5 mb-6 hover:opacity-90 transition-opacity">
            <div class="flex items-center justify-between flex-wrap gap-3">
                <div>
                    <p class="text-xs uppercase tracking-wide opacity-70">Rekomendasi Kesehatan Keuangan Terbaru ({{ ucfirst($rekomendasiTerbaru->periode_tipe) }})</p>
                    <p class="font-display text-xl mt-1">Skor {{ $rekomendasiTerbaru->skor }}/100 — {{ \App\Models\RekomendasiKesehatan::LABEL_TEKS[$rekomendasiTerbaru->label] }}</p>
                </div>
                <span class="text-sm underline">Lihat detail →</span>
            </div>
        </a>
    @else
        <div class="rounded-2xl border border-blue-100 bg-brand-50 p-5 mb-6 flex items-center justify-between flex-wrap gap-3">
            <p class="text-sm text-slate-600">Belum ada rekomendasi kesehatan keuangan. Generate yang pertama sekarang.</p>
            <a href="{{ route('rekomendasi.index') }}" class="text-sm rounded-lg bg-navy-950 text-white px-4 py-2 hover:bg-navy-800">Buat Rekomendasi</a>
        </div>
    @endif

    <div class="flex items-center gap-2 mb-6 text-sm">
        @foreach (['hari' => 'Hari ini', 'bulan' => 'Bulan ini', 'tahun' => 'Tahun ini'] as $key => $label)
            <a href="{{ route('dashboard', ['filter' => $key]) }}"
               class="px-3 py-1.5 rounded-full border {{ $filter === $key ? 'bg-navy-950 text-white border-navy-950' : 'border-slate-300 text-slate-600 hover:border-navy-950' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
        <div class="rounded-2xl border border-blue-100 bg-brand-50 p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500">Pemasukan</p>
            <p class="font-display text-2xl text-green-700 mt-1">Rp {{ number_format($ringkasan['pemasukan'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-blue-100 bg-brand-50 p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500">Pengeluaran</p>
            <p class="font-display text-2xl text-red-700 mt-1">Rp {{ number_format($ringkasan['pengeluaran'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-blue-100 bg-brand-50 p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500">Saldo</p>
            <p class="font-display text-2xl text-navy-950 mt-1">Rp {{ number_format($ringkasan['saldo'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-blue-100 bg-brand-50 p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500">Jumlah Catatan</p>
            <p class="font-display text-2xl text-navy-950 mt-1">{{ $jumlahCatatan }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 rounded-2xl border border-blue-100 p-5">
            <h2 class="font-display text-lg text-navy-950 mb-4">Pemasukan vs Pengeluaran — 7 Hari Terakhir</h2>
            <canvas id="grafik7Hari" height="120"></canvas>
        </div>

        <div class="rounded-2xl border border-blue-100 p-5">
            <h2 class="font-display text-lg text-navy-950 mb-4">Aktivitas Terbaru</h2>
            <div class="space-y-3">
                @forelse ($aktivitasTerbaru as $a)
                    <div class="text-sm border-b border-slate-100 pb-2 last:border-0">
                        <div class="flex justify-between">
                            <span>{{ str_replace('_', ' ', $a['kategori']) }}</span>
                            <span class="{{ $a['jenis'] === 'pemasukan' ? 'text-green-700' : 'text-red-700' }} font-medium">
                                {{ $a['jenis'] === 'pemasukan' ? '+' : '-' }}Rp {{ number_format($a['jumlah'], 0, ',', '.') }}
                            </span>
                        </div>
                        <span class="text-xs text-slate-400">{{ $a['tanggal']->format('d M Y') }}{{ $a['keterangan'] ? ' · ' . $a['keterangan'] : '' }}</span>
                    </div>
                @empty
                    <p class="text-sm text-slate-400">Belum ada catatan.</p>
                @endforelse
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.4/chart.umd.min.js"></script>
    <script>
        const ctx = document.getElementById('grafik7Hari');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($grafik7Hari->pluck('tanggal')),
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: @json($grafik7Hari->pluck('pemasukan')),
                        borderColor: '#2563EB',
                        backgroundColor: 'rgba(37, 99, 235, 0.1)',
                        tension: 0.3,
                        fill: true,
                    },
                    {
                        label: 'Pengeluaran',
                        data: @json($grafik7Hari->pluck('pengeluaran')),
                        borderColor: '#B91C1C',
                        backgroundColor: 'rgba(185, 28, 28, 0.08)',
                        tension: 0.3,
                        fill: true,
                    },
                ],
            },
            options: {
                responsive: true,
                plugins: { legend: { position: 'bottom' } },
                scales: { y: { beginAtZero: true } },
            },
        });
    </script>
@endsection
