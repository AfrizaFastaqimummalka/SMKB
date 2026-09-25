@extends('layouts.app')

@section('title', 'Rekomendasi Kesehatan Keuangan')
@section('subtitle', 'Dihasilkan oleh Generative AI (Gemini) berdasarkan data pemasukan & pengeluaran')

@push('styles')
<style>
    .generate-card {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        border: 1.5px solid #bfdbfe;
        border-radius: 16px;
        padding: 24px 28px;
        margin-bottom: 24px;
        display: flex;
        align-items: flex-end;
        gap: 16px;
        flex-wrap: wrap;
    }
    .generate-card .form-group { margin-bottom: 0; }

    .loading-overlay {
        display: none;
        position: fixed; inset: 0;
        background: rgba(0,0,0,.35);
        z-index: 200;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        gap: 16px;
    }
    .loading-overlay.active { display: flex; }
    .spinner {
        width: 48px; height: 48px;
        border: 4px solid rgba(255,255,255,.3);
        border-top-color: white;
        border-radius: 50%;
        animation: spin .8s linear infinite;
    }
    @keyframes spin { to { transform: rotate(360deg); } }

    .riwayat-row {
        display: grid;
        grid-template-columns: 1.6fr 1fr 1.6fr 80px 110px 120px;
        align-items: center;
        padding: 13px 18px;
        gap: 8px;
        border-bottom: 1px solid #f3f4f6;
        cursor: pointer;
        transition: background .12s;
        text-decoration: none;
        color: inherit;
    }
    .riwayat-row:last-child { border-bottom: none; }
    .riwayat-row:hover { background: #f9fafb; }

    .riwayat-header {
        display: grid;
        grid-template-columns: 1.6fr 1fr 1.6fr 80px 110px 120px;
        padding: 10px 18px;
        gap: 8px;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }
    .col-label {
        font-size: 11px;
        font-weight: 700;
        color: #9ca3af;
        letter-spacing: .05em;
        text-transform: uppercase;
    }

    .skor-bar-wrap {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .skor-bar {
        flex: 1;
        height: 6px;
        background: #e5e7eb;
        border-radius: 99px;
        overflow: hidden;
    }
    .skor-bar-fill {
        height: 100%;
        border-radius: 99px;
    }
    .skor-text {
        font-size: 13px;
        font-weight: 700;
        white-space: nowrap;
        min-width: 40px;
    }
</style>
@endpush

@section('topbar-actions')
    @if ($aiTersedia)
        <span style="display:flex;align-items:center;gap:6px;font-size:12.5px;color:#16a34a;font-weight:600;">
            <span style="width:8px;height:8px;border-radius:50%;background:#16a34a;display:inline-block;"></span>
            Gemini API aktif
        </span>
    @else
        <span style="display:flex;align-items:center;gap:6px;font-size:12.5px;color:#dc2626;font-weight:600;">
            <span style="width:8px;height:8px;border-radius:50%;background:#dc2626;display:inline-block;"></span>
            API belum dikonfigurasi
        </span>
    @endif
@endsection

@section('content')

    {{-- ── LOADING OVERLAY ── --}}
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
        <p style="color:white;font-size:14px;font-weight:600;">Sedang memproses dengan Gemini AI...</p>
        <p style="color:rgba(255,255,255,.7);font-size:12px;">Mohon tunggu beberapa detik</p>
    </div>

    {{-- ── WARNING JIKA API TIDAK ADA ── --}}
    @unless ($aiTersedia)
        <div class="alert alert-error" style="background:#fffbeb;border-color:#fde68a;color:#92400e;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                <line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            <div>GEMINI_API_KEY belum diisi — fitur generate rekomendasi belum aktif. Isi dulu di file <code style="background:#fef9c3;padding:1px 5px;border-radius:4px;">.env</code></div>
        </div>
    @endunless

    {{-- ── GENERATE FORM ── --}}
    <div class="generate-card">
        <div>
            <p style="font-size:12px;font-weight:700;color:#1d4ed8;text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px;">
                ✦ Generate Rekomendasi AI Baru
            </p>
            <p style="font-size:13px;color:#374151;max-width:420px;line-height:1.6;">
                AI akan menganalisis data keuangan periode yang dipilih dan menghasilkan skor, label, serta saran yang konkret.
            </p>
        </div>
        <form method="POST" action="{{ route('rekomendasi.store') }}"
              id="generateForm"
              style="display:flex;align-items:flex-end;gap:10px;flex-wrap:wrap;">
            @csrf
            <div class="form-group">
                <label class="form-label" style="color:#1e40af;">Periode Penilaian</label>
                <select name="periode_tipe" required class="form-select" style="min-width:210px;">
                    <option value="harian">📅 Harian (hari ini)</option>
                    <option value="mingguan">📆 Mingguan (minggu ini)</option>
                    <option value="bulanan">🗓️ Bulanan (bulan ini)</option>
                    <option value="tahunan">📊 Tahunan (tahun ini)</option>
                </select>
            </div>
            <button type="submit" @disabled(!$aiTersedia)
                    class="btn btn-primary"
                    style="padding:9px 22px;{{ !$aiTersedia ? 'opacity:.5;cursor:not-allowed;' : '' }}"
                    onclick="document.getElementById('loadingOverlay').classList.add('active')">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                </svg>
                Generate Rekomendasi
            </button>
        </form>
    </div>

    {{-- ── RIWAYAT HEADER ── --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px;">
        <div>
            <p style="font-size:15px;font-weight:800;color:#111827;">Riwayat Rekomendasi</p>
            <p style="font-size:12px;color:#6b7280;margin-top:2px;">Klik baris untuk melihat detail ringkasan & saran</p>
        </div>
        <span style="font-size:12.5px;color:#6b7280;background:#f3f4f6;border-radius:20px;padding:4px 12px;font-weight:600;">
            {{ $riwayat->total() }} total
        </span>
    </div>

    {{-- ── TABEL RIWAYAT ── --}}
    <div class="table-wrap">
        <div class="riwayat-header">
            <span class="col-label">Dibuat</span>
            <span class="col-label">Periode</span>
            <span class="col-label">Rentang Tanggal</span>
            <span class="col-label">Skor</span>
            <span class="col-label">Label</span>
            <span class="col-label">Status Penilaian</span>
        </div>

        @forelse ($riwayat as $r)
            @php
                $badgeCls = match($r->label) {
                    'sehat'           => 'badge-green',
                    'perlu_perhatian' => 'badge-amber',
                    default           => 'badge-red',
                };
                $barColor = match($r->label) {
                    'sehat'           => '#16a34a',
                    'perlu_perhatian' => '#d97706',
                    default           => '#dc2626',
                };
            @endphp
            <a href="{{ route('rekomendasi.show', $r) }}" class="riwayat-row">
                <span style="font-size:13px;color:#6b7280;">{{ $r->created_at->format('d M Y, H:i') }}</span>
                <span>
                    <span class="badge badge-blue" style="font-size:11.5px;">{{ ucfirst($r->periode_tipe) }}</span>
                </span>
                <span style="font-size:13px;color:#374151;">
                    {{ $r->tanggal_mulai->format('d/m/Y') }} – {{ $r->tanggal_selesai->format('d/m/Y') }}
                </span>
                <span>
                    <div class="skor-bar-wrap">
                        <div class="skor-bar">
                            <div class="skor-bar-fill" style="width:{{ $r->skor }}%;background:{{ $barColor }};"></div>
                        </div>
                        <span class="skor-text" style="color:{{ $barColor }};">{{ $r->skor }}</span>
                    </div>
                </span>
                <span>
                    <span class="badge {{ $badgeCls }}">
                        {{ \App\Models\RekomendasiKesehatan::LABEL_TEKS[$r->label] }}
                    </span>
                </span>
                <span>
                    @if ($r->penilaianManual)
                        <span style="display:inline-flex;align-items:center;gap:5px;font-size:12px;color:#16a34a;font-weight:600;">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                            Sudah dinilai
                        </span>
                    @else
                        <span style="font-size:12px;color:#9ca3af;font-weight:500;">Belum dinilai</span>
                    @endif
                </span>
            </a>
        @empty
            <div class="empty-state">
                <div class="empty-icon" style="background:#eff6ff;">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                    </svg>
                </div>
                <h3>Belum ada rekomendasi</h3>
                <p>Gunakan form di atas untuk generate rekomendasi AI pertama Anda</p>
            </div>
        @endforelse
    </div>

    @if ($riwayat->hasPages())
        <div style="margin-top:16px;">{{ $riwayat->links() }}</div>
    @endif

@endsection
