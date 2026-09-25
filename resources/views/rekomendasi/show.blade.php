@extends('layouts.app')

@section('title', 'Detail Rekomendasi')
@section('subtitle', ucfirst($rekomendasi->periode_tipe) . ' · ' . $rekomendasi->tanggal_mulai->format('d M Y') . ' – ' . $rekomendasi->tanggal_selesai->format('d M Y'))

@push('styles')
<style>
    .detail-wrap { max-width: 760px; }

    /* ── HERO SKOR ── */
    .skor-hero {
        border-radius: 20px;
        padding: 36px 32px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 32px;
        flex-wrap: wrap;
        position: relative;
        overflow: hidden;
    }
    .skor-hero::before {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 220px; height: 220px;
        border-radius: 50%;
        background: rgba(255,255,255,.12);
    }
    .skor-hero::after {
        content: '';
        position: absolute;
        bottom: -40px; left: 160px;
        width: 140px; height: 140px;
        border-radius: 50%;
        background: rgba(255,255,255,.08);
    }

    .skor-hero-sehat           { background: linear-gradient(135deg,#16a34a,#22c55e); }
    .skor-hero-perlu_perhatian { background: linear-gradient(135deg,#b45309,#f59e0b); }
    .skor-hero-kurang_sehat    { background: linear-gradient(135deg,#dc2626,#f87171); }

    .skor-number {
        font-size: 72px;
        font-weight: 800;
        color: white;
        line-height: 1;
        position: relative;
    }
    .skor-number sup {
        font-size: 22px;
        font-weight: 600;
        opacity: .7;
        vertical-align: super;
        margin-left: 2px;
    }
    .skor-meta { position: relative; }
    .skor-label-text {
        font-size: 22px;
        font-weight: 800;
        color: white;
        margin-bottom: 6px;
    }
    .skor-periode {
        font-size: 13px;
        color: rgba(255,255,255,.75);
        margin-bottom: 12px;
    }
    .skor-bar-wrap {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .skor-bar-bg {
        width: 200px;
        height: 8px;
        background: rgba(255,255,255,.3);
        border-radius: 99px;
        overflow: hidden;
    }
    .skor-bar-fg {
        height: 100%;
        border-radius: 99px;
        background: white;
        transition: width .8s ease;
    }
    .skor-bar-pct {
        font-size: 12px;
        font-weight: 700;
        color: rgba(255,255,255,.85);
    }

    /* ── SECTION CARD ── */
    .section-card {
        background: white;
        border: 1.5px solid #e5e7eb;
        border-radius: 16px;
        padding: 24px 26px;
        margin-bottom: 16px;
    }
    .section-card-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 14px;
    }
    .section-icon {
        width: 36px; height: 36px;
        border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .section-icon svg { width: 18px; height: 18px; }
    .section-title {
        font-size: 15px;
        font-weight: 800;
        color: #111827;
    }
    .section-body {
        font-size: 14px;
        color: #374151;
        line-height: 1.75;
        white-space: pre-line;
    }

    /* ── SARAN BULLETS ── */
    .saran-list { list-style: none; display: flex; flex-direction: column; gap: 10px; }
    .saran-item {
        display: flex;
        gap: 10px;
        align-items: flex-start;
        font-size: 14px;
        color: #374151;
        line-height: 1.6;
    }
    .saran-bullet {
        width: 22px; height: 22px;
        background: #eff6ff;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        margin-top: 1px;
        font-size: 11px;
        font-weight: 800;
        color: #2563eb;
    }

    /* ── PENILAIAN MANUAL ── */
    .penilaian-card {
        border: 1.5px solid #e5e7eb;
        border-radius: 16px;
        padding: 22px 26px;
        margin-bottom: 16px;
    }
    .compare-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-top: 14px;
    }
    .compare-item {
        border-radius: 12px;
        padding: 14px 16px;
    }
    .compare-ai     { background: #eff6ff; border: 1.5px solid #bfdbfe; }
    .compare-manual { background: #f0fdf4; border: 1.5px solid #bbf7d0; }
    .compare-label-sm {
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: .06em;
        text-transform: uppercase;
        margin-bottom: 6px;
    }
    .compare-val {
        font-size: 18px;
        font-weight: 800;
        margin-bottom: 2px;
    }
    .compare-sub { font-size: 12px; color: #6b7280; }

    .match-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 99px;
        font-size: 12px;
        font-weight: 700;
        margin-top: 12px;
    }

    /* ── BELUM DINILAI ── */
    .belum-dinilai {
        border: 1.5px dashed #d1d5db;
        border-radius: 14px;
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 16px;
    }

    /* ── META INFO ── */
    .meta-row {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        margin-bottom: 16px;
    }
    .meta-item {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12.5px;
        color: #6b7280;
    }
    .meta-item svg { width: 14px; height: 14px; flex-shrink: 0; }
    .meta-item strong { color: #374151; font-weight: 600; }
</style>
@endpush

@section('content')
@php
    $label  = $rekomendasi->label;
    $skor   = $rekomendasi->skor;
    $pm     = $rekomendasi->penilaianManual;
    $heroClass = 'skor-hero-' . $label;

    $labelTeks = \App\Models\RekomendasiKesehatan::LABEL_TEKS[$label];

    // Pecah saran jadi array kalimat
    $saranLines = array_filter(
        preg_split('/\n|(?<=\.)\s+(?=[A-Z0-9])/', trim($rekomendasi->saran)),
        fn($s) => strlen(trim($s)) > 3
    );
    if (count($saranLines) <= 1) {
        $saranLines = [$rekomendasi->saran]; // fallback satu blok
    }
@endphp

<div class="detail-wrap">

    {{-- ── HERO SKOR ── --}}
    <div class="skor-hero {{ $heroClass }}">
        <div class="skor-number">
            {{ $skor }}<sup>/100</sup>
        </div>
        <div class="skor-meta">
            <div class="skor-label-text">{{ $labelTeks }}</div>
            <div class="skor-periode">
                <svg style="display:inline;width:12px;height:12px;margin-right:4px;vertical-align:-1px;"
                     viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,.8)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
                {{ ucfirst($rekomendasi->periode_tipe) }} &nbsp;·&nbsp;
                {{ $rekomendasi->tanggal_mulai->format('d M Y') }} – {{ $rekomendasi->tanggal_selesai->format('d M Y') }}
            </div>
            <div class="skor-bar-wrap">
                <div class="skor-bar-bg">
                    <div class="skor-bar-fg" style="width:{{ $skor }}%;"></div>
                </div>
                <span class="skor-bar-pct">{{ $skor }}%</span>
            </div>
        </div>
    </div>

    {{-- ── META INFO ── --}}
    <div class="meta-row">
        <div class="meta-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
            </svg>
            Dibuat: <strong>{{ $rekomendasi->created_at->format('d M Y, H:i') }}</strong>
        </div>
        @if ($rekomendasi->dibuat_oleh)
        <div class="meta-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
            </svg>
            Oleh: <strong>{{ $rekomendasi->dibuat_oleh }}</strong>
        </div>
        @endif
        <div class="meta-item">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
            </svg>
            Model: <strong>Gemini AI</strong>
        </div>
    </div>

    {{-- ── RINGKASAN ── --}}
    <div class="section-card">
        <div class="section-card-header">
            <div class="section-icon" style="background:#eff6ff;">
                <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
            </div>
            <span class="section-title">Ringkasan Kondisi Keuangan</span>
        </div>
        <p class="section-body">{{ $rekomendasi->ringkasan }}</p>
    </div>

    {{-- ── SARAN ── --}}
    <div class="section-card">
        <div class="section-card-header">
            <div class="section-icon" style="background:#fefce8;">
                <svg viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <span class="section-title">Saran & Rekomendasi Tindakan</span>
        </div>

        @if (count($saranLines) > 1)
            <ul class="saran-list">
                @foreach ($saranLines as $i => $line)
                    @if (strlen(trim($line)) > 3)
                    <li class="saran-item">
                        <span class="saran-bullet">{{ $i + 1 }}</span>
                        <span>{{ trim($line) }}</span>
                    </li>
                    @endif
                @endforeach
            </ul>
        @else
            <p class="section-body">{{ $rekomendasi->saran }}</p>
        @endif
    </div>

    {{-- ── PENILAIAN MANUAL ── --}}
    @if ($pm)
        @php
            $labelMatch = $label === $pm->label_manual;
            $selisihSkor = abs($skor - $pm->skor_manual);
        @endphp
        <div class="penilaian-card">
            <div class="section-card-header" style="margin-bottom:6px;">
                <div class="section-icon" style="background:#f0fdf4;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 11 12 14 22 4"/>
                        <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                    </svg>
                </div>
                <span class="section-title">Penilaian Manual (Pembanding)</span>
            </div>

            <div class="compare-grid">
                <div class="compare-item compare-ai">
                    <div class="compare-label-sm" style="color:#1d4ed8;">
                        ✦ Penilaian AI
                    </div>
                    <div class="compare-val" style="color:#1d4ed8;">{{ $skor }}/100</div>
                    <div class="compare-sub">{{ $labelTeks }}</div>
                </div>
                <div class="compare-item compare-manual">
                    <div class="compare-label-sm" style="color:#15803d;">
                        👤 Penilaian Manual
                    </div>
                    <div class="compare-val" style="color:#15803d;">{{ $pm->skor_manual }}/100</div>
                    <div class="compare-sub">{{ \App\Models\RekomendasiKesehatan::LABEL_TEKS[$pm->label_manual] }}</div>
                </div>
            </div>

            <div style="display:flex;align-items:center;gap:12px;margin-top:12px;flex-wrap:wrap;">
                @if ($labelMatch)
                    <span class="match-chip" style="background:#f0fdf4;color:#15803d;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                        Label cocok
                    </span>
                @else
                    <span class="match-chip" style="background:#fef2f2;color:#dc2626;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        Label berbeda
                    </span>
                @endif
                <span style="font-size:12.5px;color:#6b7280;">
                    Selisih skor:
                    <strong style="color:{{ $selisihSkor <= 10 ? '#16a34a' : ($selisihSkor <= 20 ? '#d97706' : '#dc2626') }};">
                        {{ $selisihSkor }} poin
                    </strong>
                </span>
                @if ($pm->dinilai_oleh)
                    <span style="font-size:12.5px;color:#6b7280;">Penilai: <strong style="color:#374151;">{{ $pm->dinilai_oleh }}</strong></span>
                @endif
            </div>

            @if ($pm->catatan)
                <div style="margin-top:12px;padding:12px 16px;background:#f9fafb;border-radius:10px;border-left:3px solid #2563eb;">
                    <p style="font-size:12px;font-weight:700;color:#6b7280;margin-bottom:4px;text-transform:uppercase;letter-spacing:.04em;">Catatan</p>
                    <p style="font-size:13.5px;color:#374151;font-style:italic;">"{{ $pm->catatan }}"</p>
                </div>
            @endif
        </div>

    @else
        <div class="belum-dinilai">
            <div style="display:flex;align-items:center;gap:12px;">
                <div style="width:36px;height:36px;background:#f3f4f6;border-radius:9px;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#9ca3af" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </div>
                <div>
                    <p style="font-size:13.5px;font-weight:600;color:#374151;">Belum ada penilaian manual</p>
                    <p style="font-size:12px;color:#9ca3af;margin-top:2px;">Tambahkan penilaian manual untuk membandingkan akurasi AI</p>
                </div>
            </div>
            <a href="{{ route('uji-akurasi.index') }}" class="btn btn-primary btn-sm">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                Isi di Halaman Uji Akurasi
            </a>
        </div>
    @endif

    {{-- ── BACK LINK ── --}}
    <a href="{{ route('rekomendasi.index') }}"
       style="display:inline-flex;align-items:center;gap:6px;font-size:13.5px;font-weight:600;
              color:#6b7280;text-decoration:none;margin-top:8px;transition:color .15s;"
       onmouseover="this.style.color='#111827'" onmouseout="this.style.color='#6b7280'">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/>
        </svg>
        Kembali ke Riwayat
    </a>

</div>
@endsection
