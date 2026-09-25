@extends('layouts.app')

@section('title', 'Laporan Keuangan')
@section('subtitle', 'Lihat dan export laporan berdasarkan periode')

@section('content')

    {{-- ── FILTER PERIODE ── --}}
    <div class="card" style="margin-bottom:18px;">
        <div style="display:flex;align-items:center;gap:6px;margin-bottom:16px;flex-wrap:wrap;">
            @foreach(['harian' => 'Harian', 'bulanan' => 'Bulanan', 'tahunan' => 'Tahunan'] as $key => $label)
                <a href="{{ route('laporan.index', ['periode' => $key]) }}"
                   style="padding:7px 18px;border-radius:8px;font-size:13px;font-weight:600;text-decoration:none;transition:all .15s;
                          {{ $periode === $key
                             ? 'background:#2563eb;color:white;border:1.5px solid #2563eb;'
                             : 'background:white;color:#374151;border:1.5px solid #e5e7eb;' }}"
                   onmouseover="{{ $periode !== $key ? 'this.style.borderColor=\"#2563eb\";this.style.color=\"#2563eb\"' : '' }}"
                   onmouseout="{{ $periode !== $key ? 'this.style.borderColor=\"#e5e7eb\";this.style.color=\"#374151\"' : '' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <form method="GET" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
            <input type="hidden" name="periode" value="{{ $periode }}">
            @if ($periode === 'harian')
                <input type="date" name="dari" value="{{ request('dari', $dari->toDateString()) }}"
                       class="form-input" style="width:auto;padding:7px 12px;">
                <input type="date" name="sampai" value="{{ request('sampai', $sampai->toDateString()) }}"
                       class="form-input" style="width:auto;padding:7px 12px;">
            @elseif ($periode === 'bulanan')
                <div style="display:flex;align-items:center;gap:6px;">
                    <label style="font-size:13px;font-weight:600;color:#374151;">Bulan</label>
                    <select name="bulan" class="form-select" style="width:auto;padding:7px 30px 7px 11px;">
                        @foreach(range(1,12) as $b)
                            <option value="{{ $b }}" {{ request('bulan', $dari->month) == $b ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="display:flex;align-items:center;gap:6px;">
                    <label style="font-size:13px;font-weight:600;color:#374151;">Tahun</label>
                    <select name="tahun" class="form-select" style="width:auto;padding:7px 30px 7px 11px;">
                        @foreach(range(now()->year - 3, now()->year + 1) as $y)
                            <option value="{{ $y }}" {{ request('tahun', $dari->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            @else
                <select name="tahun" class="form-select" style="width:auto;padding:7px 30px 7px 11px;">
                    @foreach(range(now()->year - 3, now()->year + 1) as $y)
                        <option value="{{ $y }}" {{ request('tahun', $dari->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            @endif
            <button type="submit" class="btn btn-primary btn-sm">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                Lihat Laporan
            </button>
        </form>
    </div>

    {{-- ── STAT CARDS ── --}}
    <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:18px;">
        <div class="stat-card">
            <div>
                <div class="stat-label">Total Pemasukan</div>
                <div class="stat-value" style="color:#16a34a;">Rp {{ number_format($ringkasan['pemasukan'], 0, ',', '.') }}</div>
            </div>
            <div class="stat-icon stat-icon-green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/>
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
                    <polyline points="23 18 13.5 8.5 8.5 13.5 1 6"/><polyline points="17 18 23 18 23 12"/>
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
                    <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                </svg>
            </div>
        </div>
    </div>

    {{-- ── EXPORT BUTTON ── --}}
    <div style="display:flex;justify-content:flex-end;margin-bottom:16px;">
        <a href="{{ route('laporan.export', request()->query()) }}" class="btn btn-outline btn-sm">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                <polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>
            </svg>
            Export Excel (.xlsx)
        </a>
    </div>

    {{-- ── TABEL PEMASUKAN ── --}}
    <p style="font-size:15px;font-weight:800;color:#111827;margin-bottom:10px;">
        Detail Pemasukan <span style="font-size:13px;font-weight:500;color:#6b7280;">({{ $pemasukan->count() }} transaksi)</span>
    </p>
    <div class="table-wrap" style="margin-bottom:20px;">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Keterangan</th>
                    <th style="text-align:right;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pemasukan as $p)
                    <tr>
                        <td>{{ $p->tanggal->format('d/m/Y') }}</td>
                        <td><span class="badge badge-blue">{{ str($p->kategori)->headline() }}</span></td>
                        <td style="color:#6b7280;">{{ $p->keterangan ?? '—' }}</td>
                        <td style="text-align:right;font-weight:600;color:#16a34a;">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align:center;color:#9ca3af;padding:24px;">Tidak ada pemasukan di periode ini</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ── TABEL PENGELUARAN ── --}}
    <p style="font-size:15px;font-weight:800;color:#111827;margin-bottom:10px;">
        Detail Pengeluaran <span style="font-size:13px;font-weight:500;color:#6b7280;">({{ $pengeluaran->count() }} transaksi)</span>
    </p>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Keterangan</th>
                    <th style="text-align:right;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pengeluaran as $p)
                    <tr>
                        <td>{{ $p->tanggal->format('d/m/Y') }}</td>
                        <td><span class="badge badge-red">{{ str($p->kategori)->headline() }}</span></td>
                        <td style="color:#6b7280;">{{ $p->keterangan ?? '—' }}</td>
                        <td style="text-align:right;font-weight:600;color:#dc2626;">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align:center;color:#9ca3af;padding:24px;">Tidak ada pengeluaran di periode ini</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection
