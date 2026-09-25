@extends('layouts.app')

@section('title', 'Detail Rekomendasi')
@section('subtitle', ucfirst($rekomendasi->periode_tipe) . ' · ' . $rekomendasi->tanggal_mulai->format('d M Y') . ' - ' . $rekomendasi->tanggal_selesai->format('d M Y'))

@section('content')
    @php
        $warna = match($rekomendasi->label) {
            'sehat' => 'bg-green-50 text-green-700 border-green-200',
            'perlu_perhatian' => 'bg-amber-50 text-amber-700 border-amber-200',
            default => 'bg-red-50 text-red-700 border-red-200',
        };
    @endphp

    <div class="max-w-2xl">
        <div class="rounded-2xl border {{ $warna }} p-6 mb-6 text-center">
            <p class="text-xs uppercase tracking-wide opacity-70 mb-1">Skor Kesehatan Keuangan</p>
            <p class="font-display text-5xl mb-2">{{ $rekomendasi->skor }}<span class="text-xl opacity-60">/100</span></p>
            <p class="text-lg font-medium">{{ \App\Models\RekomendasiKesehatan::LABEL_TEKS[$rekomendasi->label] }}</p>
        </div>

        <div class="rounded-2xl border border-blue-100 p-5 mb-4">
            <h2 class="font-display text-lg text-navy-950 mb-2">Ringkasan</h2>
            <p class="text-sm text-slate-700 whitespace-pre-line leading-relaxed">{{ $rekomendasi->ringkasan }}</p>
        </div>

        <div class="rounded-2xl border border-blue-100 p-5 mb-6">
            <h2 class="font-display text-lg text-navy-950 mb-2">Saran</h2>
            <p class="text-sm text-slate-700 whitespace-pre-line leading-relaxed">{{ $rekomendasi->saran }}</p>
        </div>

        @if ($rekomendasi->penilaianManual)
            <div class="rounded-2xl border border-blue-100 bg-brand-50 p-5">
                <h2 class="font-display text-lg text-navy-950 mb-2">Penilaian Manual (Pembanding)</h2>
                <div class="text-sm text-slate-700 space-y-1">
                    <p>Skor manual: <span class="font-medium">{{ $rekomendasi->penilaianManual->skor_manual }}/100</span></p>
                    <p>Label manual: <span class="font-medium">{{ \App\Models\RekomendasiKesehatan::LABEL_TEKS[$rekomendasi->penilaianManual->label_manual] }}</span></p>
                    @if ($rekomendasi->penilaianManual->catatan)
                        <p class="text-slate-500 mt-2">"{{ $rekomendasi->penilaianManual->catatan }}"</p>
                    @endif
                </div>
            </div>
        @else
            <div class="rounded-2xl border border-slate-200 p-5 flex items-center justify-between flex-wrap gap-3">
                <p class="text-sm text-slate-500">Belum ada penilaian manual pembanding untuk rekomendasi ini.</p>
                <a href="{{ route('uji-akurasi.index') }}" class="text-sm text-brand-600 font-medium hover:underline">Isi di halaman Uji Akurasi →</a>
            </div>
        @endif

        <a href="{{ route('rekomendasi.index') }}" class="inline-block mt-6 text-sm text-slate-500 hover:text-navy-950">← Kembali ke riwayat</a>
    </div>
@endsection
