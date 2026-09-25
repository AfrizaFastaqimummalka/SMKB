@extends('layouts.app')

@section('title', 'Uji Akurasi')
@section('subtitle', 'Bandingkan penilaian AI dengan penilaian manual pihak keuangan')

@push('styles')
<style>
    /* ── STAT CARDS ── */
    .akurasi-stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
        margin-bottom: 24px;
    }
    .akurasi-stat {
        background: white;
        border: 1.5px solid #e5e7eb;
        border-radius: 14px;
        padding: 20px 22px;
    }
    .akurasi-stat .label {
        font-size: 11px;
        font-weight: 700;
        color: #9ca3af;
        letter-spacing: .05em;
        text-transform: uppercase;
        margin-bottom: 10px;
    }
    .akurasi-stat .value {
        font-size: 28px;
        font-weight: 800;
        color: #111827;
        line-height: 1;
        margin-bottom: 6px;
    }
    .akurasi-stat .sub {
        font-size: 12px;
        color: #9ca3af;
    }

    /* Progress ring untuk kecocokan */
    .progress-ring-wrap {
        display: flex;
        align-items: center;
        gap: 14px;
    }
    .ring-svg { transform: rotate(-90deg); }
    .ring-track { fill: none; stroke: #e5e7eb; }
    .ring-fill  { fill: none; stroke-linecap: round; transition: stroke-dashoffset .6s ease; }

    /* ── ITEM REKOMENDASI ── */
    .rek-item {
        background: white;
        border: 1.5px solid #e5e7eb;
        border-radius: 14px;
        overflow: hidden;
        margin-bottom: 12px;
        transition: box-shadow .15s;
    }
    .rek-item:hover { box-shadow: 0 2px 12px rgba(0,0,0,.07); }

    .rek-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        gap: 12px;
        flex-wrap: wrap;
    }

    .rek-meta { flex: 1; min-width: 0; }
    .rek-periode {
        font-size: 11.5px;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: 6px;
    }
    .rek-scores {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }
    .score-pill {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: 9px;
        font-size: 13px;
        font-weight: 600;
    }
    .score-ai     { background: #eff6ff; color: #1d4ed8; }
    .score-manual { background: #f0fdf4; color: #15803d; }

    .match-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 11.5px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 99px;
    }
    .match-yes { background: #f0fdf4; color: #15803d; }
    .match-no  { background: #fef2f2; color: #dc2626; }

    /* ── FORM PENILAIAN ── */
    .penilaian-form {
        background: #f9fafb;
        border-top: 1px solid #f3f4f6;
        padding: 16px 20px;
        display: none;
    }
    .penilaian-form.open { display: block; }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
        gap: 12px;
        margin-bottom: 12px;
    }

    .btn-nilai {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 16px;
        border: 1.5px solid #2563eb;
        background: white;
        color: #2563eb;
        font-size: 13px;
        font-weight: 700;
        border-radius: 8px;
        cursor: pointer;
        font-family: 'Plus Jakarta Sans', sans-serif;
        transition: all .15s;
    }
    .btn-nilai:hover { background: #2563eb; color: white; }
    .btn-nilai.open  { background: #2563eb; color: white; }

    @media (max-width: 768px) {
        .akurasi-stats { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('content')

    {{-- ── STAT CARDS ── --}}
    @php
        $total    = $ringkasanAkurasi['total_dinilai'];
        $cocok    = $ringkasanAkurasi['label_cocok'];
        $pct      = $ringkasanAkurasi['persentase_kecocokan_label'];
        $selisih  = $ringkasanAkurasi['rata_rata_selisih_skor'];
        $r = 38; 
        $circ = 239; // 2 * pi * 38
        $offset = (int) round($circ - ($pct / 100) * $circ);
        $pctColor = $pct >= 70 ? '#16a34a' : ($pct >= 40 ? '#d97706' : '#dc2626');
    @endphp

    <div class="akurasi-stats">
        {{-- Total Dinilai --}}
        <div class="akurasi-stat">
            <div class="label">Total Dinilai</div>
            <div class="value" style="color:#2563eb;">{{ $total }}</div>
            <div class="sub">rekomendasi sudah mendapat penilaian manual</div>
        </div>

        {{-- Kecocokan Label --}}
        <div class="akurasi-stat">
            <div class="label">Kecocokan Label</div>
            <div class="progress-ring-wrap">
                <svg width="90" height="90" class="ring-svg">
                    <circle class="ring-track" cx="45" cy="45" r="{{ $r }}" stroke-width="7"/>
                    <circle class="ring-fill" cx="45" cy="45" r="{{ $r }}" stroke-width="7"
                            stroke="{{ $pctColor }}"
                            stroke-dasharray="{{ $circ }}"
                            stroke-dashoffset="{{ $total > 0 ? $offset : $circ }}"/>
                </svg>
                <div>
                    <div class="value" style="font-size:24px;color:{{ $pctColor }};">
                        {{ $pct }}%
                    </div>
                    <div class="sub">{{ $cocok }} dari {{ $total }} cocok</div>
                </div>
            </div>
        </div>

        {{-- Rata-rata Selisih Skor --}}
        <div class="akurasi-stat">
            <div class="label">Rata-rata Selisih Skor</div>
            <div style="display:flex;align-items:flex-end;gap:6px;">
                <div class="value" style="color:{{ $selisih <= 10 ? '#16a34a' : ($selisih <= 20 ? '#d97706' : '#dc2626') }};">
                    {{ $selisih }}
                </div>
                <div style="font-size:13px;color:#9ca3af;margin-bottom:4px;">/ 100 poin</div>
            </div>
            <div class="sub">
                @if ($selisih <= 10) Akurasi sangat baik ✓
                @elseif ($selisih <= 20) Akurasi cukup baik
                @else Perlu ditinjau ulang
                @endif
            </div>
        </div>
    </div>

    {{-- ── DAFTAR REKOMENDASI ── --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;">
        <div>
            <p style="font-size:15px;font-weight:800;color:#111827;">Daftar Rekomendasi</p>
            <p style="font-size:12px;color:#6b7280;margin-top:2px;">Klik "Nilai Manual" untuk membandingkan dengan penilaian AI</p>
        </div>
    </div>

    @forelse ($daftar as $r)
        @php
            $aiColor = match($r->label) {
                'sehat'           => '#1d4ed8',
                'perlu_perhatian' => '#b45309',
                default           => '#dc2626',
            };
            $pm = $r->penilaianManual;
            $labelMatch = $pm && ($r->label === $pm->label_manual);
        @endphp

        <div class="rek-item" id="item-{{ $r->id }}">
            <div class="rek-header">
                <div class="rek-meta">
                    <div class="rek-periode">
                        {{ ucfirst($r->periode_tipe) }} &nbsp;·&nbsp;
                        {{ $r->tanggal_mulai->format('d/m/Y') }} – {{ $r->tanggal_selesai->format('d/m/Y') }}
                    </div>
                    <div class="rek-scores">
                        {{-- Skor AI --}}
                        <div class="score-pill score-ai">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>
                            </svg>
                            AI: {{ $r->skor }}/100 —
                            {{ \App\Models\RekomendasiKesehatan::LABEL_TEKS[$r->label] }}
                        </div>

                        {{-- Skor Manual (kalau sudah ada) --}}
                        @if ($pm)
                            <div class="score-pill score-manual">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                Manual: {{ $pm->skor_manual }}/100 —
                                {{ \App\Models\RekomendasiKesehatan::LABEL_TEKS[$pm->label_manual] }}
                            </div>

                            {{-- Badge cocok/tidak --}}
                            <span class="match-badge {{ $labelMatch ? 'match-yes' : 'match-no' }}">
                                @if ($labelMatch)
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    Label cocok
                                @else
                                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    Label beda
                                @endif
                            </span>
                        @endif
                    </div>
                </div>

                {{-- Aksi --}}
                <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;">
                    @if ($pm)
                        <form method="POST" action="{{ route('uji-akurasi.destroy', $pm) }}"
                              onsubmit="return confirm('Hapus penilaian manual ini?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Hapus Penilaian</button>
                        </form>
                    @else
                        <button type="button"
                                class="btn-nilai"
                                id="btn-{{ $r->id }}"
                                onclick="toggleForm({{ $r->id }})">
                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                            </svg>
                            Nilai Manual
                        </button>
                    @endif
                </div>
            </div>

            {{-- Form penilaian (tersembunyi) --}}
            @unless ($pm)
                <div class="penilaian-form" id="form-{{ $r->id }}">
                    <p style="font-size:12.5px;font-weight:700;color:#374151;margin-bottom:12px;">
                        Isi penilaian manual untuk rekomendasi ini:
                    </p>
                    <form method="POST" action="{{ route('uji-akurasi.store', $r) }}">
                        @csrf
                        <div class="form-row">
                            <div>
                                <label class="form-label" style="font-size:12px;">Skor Manual (0–100)</label>
                                <input type="number" name="skor_manual" min="0" max="100" required
                                       placeholder="Contoh: 75"
                                       class="form-input" style="padding:8px 12px;">
                            </div>
                            <div>
                                <label class="form-label" style="font-size:12px;">Label Manual</label>
                                <select name="label_manual" required class="form-select" style="padding:8px 12px;">
                                    <option value="sehat">✅ Sehat</option>
                                    <option value="perlu_perhatian">⚠️ Perlu Perhatian</option>
                                    <option value="kurang_sehat">❌ Kurang Sehat</option>
                                </select>
                            </div>
                            <div>
                                <label class="form-label" style="font-size:12px;">Dinilai Oleh</label>
                                <input type="text" name="dinilai_oleh" placeholder="Nama penilai"
                                       class="form-input" style="padding:8px 12px;">
                            </div>
                        </div>
                        <div style="margin-bottom:12px;">
                            <label class="form-label" style="font-size:12px;">Catatan <span style="color:#9ca3af;font-weight:400;">(opsional)</span></label>
                            <textarea name="catatan" rows="2" placeholder="Alasan atau pertimbangan penilaian..."
                                      class="form-input" style="resize:vertical;"></textarea>
                        </div>
                        <div style="display:flex;gap:8px;">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                Simpan Penilaian
                            </button>
                            <button type="button" onclick="toggleForm({{ $r->id }})"
                                    class="btn btn-outline btn-sm">Batal</button>
                        </div>
                    </form>
                </div>
            @endunless
        </div>
    @empty
        <div class="empty-state">
            <div class="empty-icon" style="background:#eff6ff;">
                <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 11 12 14 22 4"/>
                    <path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                </svg>
            </div>
            <h3>Belum ada rekomendasi</h3>
            <p>Generate dulu rekomendasi di halaman Rekomendasi AI</p>
            <a href="{{ route('rekomendasi.index') }}" class="btn btn-primary btn-sm">
                Ke Halaman Rekomendasi
            </a>
        </div>
    @endforelse

    @if ($daftar->hasPages())
        <div style="margin-top:16px;">{{ $daftar->links() }}</div>
    @endif

@endsection

@push('scripts')
<script>
function toggleForm(id) {
    const form = document.getElementById('form-' + id);
    const btn  = document.getElementById('btn-'  + id);
    if (!form) return;
    form.classList.toggle('open');
    btn.classList.toggle('open');
    btn.innerHTML = form.classList.contains('open')
        ? `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg> Tutup Form`
        : `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg> Nilai Manual`;
}
</script>
@endpush
