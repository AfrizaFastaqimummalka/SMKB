@extends('layouts.app')

@section('title', 'Dashboard')
@section('subtitle', now()->format('F Y'))

@push('styles')
<style>
    .filter-tabs { display: flex; gap: 6px; }
    .filter-tab {
        padding: 7px 16px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        border: 1.5px solid #e5e7eb;
        background: white;
        color: #374151;
        cursor: pointer;
        text-decoration: none;
        transition: all .15s;
    }
    .filter-tab:hover { border-color: #2563eb; color: #2563eb; }
    .filter-tab.active { background: #2563eb; border-color: #2563eb; color: white; }

    .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; margin-bottom: 22px; }

    .quick-actions { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 12px; margin-top: 20px; }
    .quick-btn {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 18px;
        background: white;
        border: 1.5px solid #e5e7eb;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        text-decoration: none;
        transition: border-color .15s, background .15s;
        font-family: 'Plus Jakarta Sans', sans-serif;
        cursor: pointer;
    }
    .quick-btn:hover { border-color: #2563eb; background: #eff6ff; color: #1d4ed8; }
    .quick-btn-icon {
        width: 36px; height: 36px;
        border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .quick-btn-icon svg { width: 18px; height: 18px; }

    .chart-section { display: grid; grid-template-columns: 1fr; gap: 16px; margin-bottom: 0; }

    @media (max-width: 900px) {
        .stats-grid { grid-template-columns: 1fr; }
    }
</style>
@endpush

@section('topbar-actions')
    <div class="filter-tabs">
        @foreach(['hari' => 'Hari Ini', 'bulan' => 'Bulan Ini', 'tahun' => 'Tahun Ini'] as $key => $label)
            <a href="{{ route('dashboard', ['filter' => $key]) }}"
               class="filter-tab {{ $filter === $key ? 'active' : '' }}">
                {{ $label }}
            </a>
        @endforeach
        <a href="{{ route('dashboard') }}" title="Refresh"
           style="padding:7px 10px;border:1.5px solid #e5e7eb;border-radius:8px;background:white;color:#6b7280;display:flex;align-items:center;text-decoration:none;transition:all .15s;"
           onmouseover="this.style.borderColor='#2563eb'" onmouseout="this.style.borderColor='#e5e7eb'">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/>
                <path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/>
            </svg>
        </a>
    </div>
@endsection

@section('content')

    {{-- ── BANNER REKOMENDASI ── --}}
    @if ($rekomendasiTerbaru)
        @php
            $cls = match($rekomendasiTerbaru->label) {
                'sehat'          => ['bg'=>'#f0fdf4','bd'=>'#bbf7d0','tx'=>'#15803d','ic'=>'#16a34a'],
                'perlu_perhatian'=> ['bg'=>'#fffbeb','bd'=>'#fde68a','tx'=>'#b45309','ic'=>'#d97706'],
                default          => ['bg'=>'#fef2f2','bd'=>'#fecaca','tx'=>'#dc2626','ic'=>'#dc2626'],
            };
        @endphp
        <a href="{{ route('rekomendasi.show', $rekomendasiTerbaru) }}"
           style="display:flex;align-items:center;justify-content:space-between;gap:12px;
                  background:{{ $cls['bg'] }};border:1.5px solid {{ $cls['bd'] }};
                  border-radius:12px;padding:14px 18px;margin-bottom:20px;
                  text-decoration:none;transition:opacity .15s;"
           onmouseover="this.style.opacity='.88'" onmouseout="this.style.opacity='1'">
            <div>
                <p style="font-size:11px;font-weight:700;color:{{ $cls['tx'] }};opacity:.7;letter-spacing:.05em;text-transform:uppercase;margin-bottom:4px;">
                    Rekomendasi Terbaru — {{ ucfirst($rekomendasiTerbaru->periode_tipe) }}
                </p>
                <p style="font-size:15px;font-weight:800;color:{{ $cls['tx'] }};">
                    Skor {{ $rekomendasiTerbaru->skor }}/100 — {{ \App\Models\RekomendasiKesehatan::LABEL_TEKS[$rekomendasiTerbaru->label] }}
                </p>
            </div>
            <span style="font-size:13px;font-weight:600;color:{{ $cls['tx'] }};white-space:nowrap;">Lihat detail →</span>
        </a>
    @else
        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;
                    background:#eff6ff;border:1.5px solid #bfdbfe;border-radius:12px;
                    padding:14px 18px;margin-bottom:20px;">
            <p style="font-size:13.5px;color:#374151;">Belum ada rekomendasi kesehatan keuangan. Generate yang pertama sekarang.</p>
            <a href="{{ route('rekomendasi.index') }}"
               style="font-size:13px;font-weight:700;background:#2563eb;color:white;
                      padding:8px 16px;border-radius:8px;text-decoration:none;white-space:nowrap;transition:background .15s;"
               onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                Buat Rekomendasi
            </a>
        </div>
    @endif

    {{-- ── STAT CARDS ── --}}
    <div class="stats-grid">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Pemasukan</div>
                <div class="stat-value" style="color:#16a34a;">Rp {{ number_format($ringkasan['pemasukan'], 0, ',', '.') }}</div>
            </div>
            <div class="stat-icon stat-icon-green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                </svg>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <div class="stat-label">Total Pengeluaran</div>
                <div class="stat-value" style="color:#dc2626;">Rp {{ number_format($ringkasan['pengeluaran'], 0, ',', '.') }}</div>
            </div>
            <div class="stat-icon stat-icon-red">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="1" y="4" width="22" height="16" rx="2"/><line x1="1" y1="10" x2="23" y2="10"/>
                </svg>
            </div>
        </div>

        <div class="stat-card">
            <div>
                <div class="stat-label">Saldo Bersih</div>
                <div class="stat-value" style="color:{{ $ringkasan['saldo'] >= 0 ? '#2563eb' : '#dc2626' }};">
                    Rp {{ number_format($ringkasan['saldo'], 0, ',', '.') }}
                </div>
            </div>
            <div class="stat-icon stat-icon-blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- ── GRAFIK 7 HARI ── --}}
    <div class="card" style="margin-bottom:16px;">
        <p style="font-size:15px;font-weight:800;color:#111827;margin-bottom:4px;">Grafik 7 Hari Terakhir</p>
        <p style="font-size:12px;color:#2563eb;margin-bottom:16px;">Pemasukan vs pengeluaran harian</p>
        <div style="position:relative;height:220px;">
            <canvas id="grafik7Hari"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        var raw = @json($grafik7Hari->values()->all());

        // Format tanggal jadi "19/09"
        var labels = raw.map(function(d) {
            var parts = d.tanggal.split(' ');
            var months = {'Jan':'01','Feb':'02','Mar':'03','Apr':'04','Mei':'05','Jun':'06',
                          'Jul':'07','Agu':'08','Sep':'09','Okt':'10','Nov':'11','Des':'12',
                          'May':'05','Aug':'08','Oct':'10','Dec':'12'};
            var m = months[parts[1]] || '??';
            return parts[0] + '/' + m;
        });
        var pemasukan   = raw.map(function(d){ return parseFloat(d.pemasukan)   || 0; });
        var pengeluaran = raw.map(function(d){ return parseFloat(d.pengeluaran) || 0; });

        function singkat(v) {
            if (v >= 1000000) return (v/1000000).toFixed(v%1000000===0?0:1) + 'jt';
            if (v >= 1000)    return (v/1000).toFixed(v%1000===0?0:0)       + 'rb';
            return v.toString();
        }

        if (typeof Chart === 'undefined') {
            console.error("Chart.js gagal di-load!");
            return;
        }

        new Chart(document.getElementById('grafik7Hari'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: pemasukan,
                        backgroundColor: '#16a34a',
                        borderRadius: 5,
                        borderSkipped: false,
                    },
                    {
                        label: 'Pengeluaran',
                        data: pengeluaran,
                        backgroundColor: '#ef4444',
                        borderRadius: 5,
                        borderSkipped: false,
                    },
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { family: 'Plus Jakarta Sans', size: 12 },
                            boxWidth: 12, padding: 16,
                            usePointStyle: true, pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                return ' ' + ctx.dataset.label + ': Rp ' + ctx.parsed.y.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f3f4f6' },
                        border: { display: false },
                        ticks: {
                            font: { family: 'Plus Jakarta Sans', size: 11 },
                            color: '#9ca3af',
                            callback: function(v) { return singkat(v); }
                        }
                    },
                    x: {
                        grid: { display: false },
                        border: { display: false },
                        ticks: {
                            font: { family: 'Plus Jakarta Sans', size: 11 },
                            color: '#6b7280'
                        }
                    },
                },
                barPercentage: 0.6,
                categoryPercentage: 0.7,
            },
        });
    });
    </script>

    {{-- ── QUICK ACTIONS ── --}}
    <div class="quick-actions">
        <a href="{{ route('pemasukan.create') }}" class="quick-btn">
            <div class="quick-btn-icon" style="background:#f0fdf4;">
                <svg viewBox="0 0 24 24" fill="none" stroke="#16a34a" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="16"/><line x1="8" y1="12" x2="16" y2="12"/>
                </svg>
            </div>
            Tambah Pemasukan
        </a>
        <a href="{{ route('pengeluaran.create') }}" class="quick-btn">
            <div class="quick-btn-icon" style="background:#fef2f2;">
                <svg viewBox="0 0 24 24" fill="none" stroke="#dc2626" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/>
                </svg>
            </div>
            Tambah Pengeluaran
        </a>
        <a href="{{ route('laporan.index') }}" class="quick-btn">
            <div class="quick-btn-icon" style="background:#eff6ff;">
                <svg viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/>
                </svg>
            </div>
            Lihat Laporan
        </a>
        <a href="{{ route('rekomendasi.index') }}" class="quick-btn">
            <div class="quick-btn-icon" style="background:#fefce8;">
                <svg viewBox="0 0 24 24" fill="none" stroke="#d97706" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                </svg>
            </div>
            Rekomendasi AI
        </a>
    </div>

@endsection
