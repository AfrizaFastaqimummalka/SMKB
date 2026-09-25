@extends('layouts.app')

@section('title', 'Pemasukan')
@section('subtitle', count($pemasukan) . ' transaksi — Total: Rp ' . number_format($pemasukan->sum('jumlah'), 0, ',', '.'))

@section('topbar-actions')
    <a href="{{ route('pemasukan.create') }}" class="btn btn-primary">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
        </svg>
        Tambah Pemasukan
    </a>
@endsection

@section('content')

    {{-- Filter Bar --}}
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:20px;flex-wrap:wrap;">
        <form method="GET" style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
            <div style="display:flex;align-items:center;gap:6px;">
                <label style="font-size:13px;font-weight:600;color:#374151;">Bulan:</label>
                <select name="bulan" class="form-select" style="width:auto;padding:7px 30px 7px 11px;">
                    @foreach(range(1,12) as $b)
                        <option value="{{ $b }}" {{ request('bulan', now()->month) == $b ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($b)->translatedFormat('M') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="display:flex;align-items:center;gap:6px;">
                <label style="font-size:13px;font-weight:600;color:#374151;">Tahun:</label>
                <select name="tahun" class="form-select" style="width:auto;padding:7px 30px 7px 11px;">
                    @foreach(range(now()->year - 2, now()->year + 1) as $y)
                        <option value="{{ $y }}" {{ request('tahun', now()->year) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-outline btn-sm">Tampilkan</button>
        </form>
    </div>

    {{-- Table --}}
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:40px;">No</th>
                    <th>Tanggal</th>
                    <th>Kategori</th>
                    <th>Keterangan</th>
                    <th style="text-align:right;">Jumlah</th>
                    <th style="text-align:center;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pemasukan as $i => $p)
                    <tr>
                        <td style="color:#9ca3af;">{{ $pemasukan->firstItem() + $i }}</td>
                        <td>{{ $p->tanggal->format('d/m/Y') }}</td>
                        <td>
                            <span class="badge badge-blue">{{ str($p->kategori)->headline() }}</span>
                        </td>
                        <td style="color:#6b7280;">{{ $p->keterangan ?? '—' }}</td>
                        <td style="text-align:right;font-weight:700;color:#16a34a;">
                            Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                        </td>
                        <td style="text-align:center;">
                            <form method="POST" action="{{ route('pemasukan.destroy', $p) }}"
                                  onsubmit="return confirm('Hapus data pemasukan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="padding:0;">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/>
                                        <polyline points="14 2 14 8 20 8"/>
                                    </svg>
                                </div>
                                <h3>Belum ada data pemasukan</h3>
                                <p>Klik tombol Tambah Pemasukan untuk mulai mencatat</p>
                                <a href="{{ route('pemasukan.create') }}" class="btn btn-primary btn-sm">
                                    + Tambah Sekarang
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($pemasukan->hasPages())
        <div style="margin-top:16px;">{{ $pemasukan->links() }}</div>
    @endif

@endsection
