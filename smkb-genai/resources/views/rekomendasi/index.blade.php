@extends('layouts.app')

@section('title', 'Rekomendasi Kesehatan Keuangan')
@section('subtitle', 'Dihasilkan oleh Generative AI (Gemini) berdasarkan data pemasukan & pengeluaran')

@section('content')
    @unless ($aiTersedia)
        <div class="mb-6 rounded-lg border border-amber-200 bg-amber-50 text-amber-800 text-sm px-4 py-3">
            API key Gemini belum diisi — fitur generate rekomendasi belum bisa dipakai. Riwayat lama (kalau ada) tetap bisa dilihat.
        </div>
    @endunless

    <form method="POST" action="{{ route('rekomendasi.store') }}" class="rounded-2xl border border-blue-100 bg-brand-50 p-5 mb-8 flex items-end gap-3 flex-wrap">
        @csrf
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Periode Penilaian</label>
            <select name="periode_tipe" required class="rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-600">
                <option value="harian">Harian (hari ini)</option>
                <option value="mingguan">Mingguan (minggu ini)</option>
                <option value="bulanan">Bulanan (bulan ini)</option>
                <option value="tahunan">Tahunan (tahun ini)</option>
            </select>
        </div>
        <button type="submit" @if (!$aiTersedia) disabled @endif
                class="rounded-lg bg-navy-950 text-white text-sm font-medium px-4 py-2 hover:bg-navy-800 disabled:opacity-50 disabled:cursor-not-allowed">
            Generate Rekomendasi
        </button>
    </form>

    <h2 class="font-display text-lg text-navy-950 mb-3">Riwayat Rekomendasi</h2>
    <div class="rounded-2xl border border-blue-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-brand-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium">Dibuat</th>
                    <th class="px-4 py-3 font-medium">Periode</th>
                    <th class="px-4 py-3 font-medium">Rentang Tanggal</th>
                    <th class="px-4 py-3 font-medium">Skor</th>
                    <th class="px-4 py-3 font-medium">Label</th>
                    <th class="px-4 py-3 font-medium">Penilaian Manual</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($riwayat as $r)
                    <tr class="hover:bg-brand-50/50 cursor-pointer" onclick="window.location='{{ route('rekomendasi.show', $r) }}'">
                        <td class="px-4 py-3 text-slate-500">{{ $r->created_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3 text-slate-700 capitalize">{{ $r->periode_tipe }}</td>
                        <td class="px-4 py-3 text-slate-600">{{ $r->tanggal_mulai->format('d/m/Y') }} - {{ $r->tanggal_selesai->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 font-medium text-navy-950">{{ $r->skor }}/100</td>
                        <td class="px-4 py-3">
                            @php
                                $warna = match($r->label) {
                                    'sehat' => 'bg-green-50 text-green-700',
                                    'perlu_perhatian' => 'bg-amber-50 text-amber-700',
                                    default => 'bg-red-50 text-red-700',
                                };
                            @endphp
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $warna }}">{{ \App\Models\RekomendasiKesehatan::LABEL_TEKS[$r->label] }}</span>
                        </td>
                        <td class="px-4 py-3 text-slate-500">
                            @if ($r->penilaianManual)
                                <span class="text-xs px-2 py-0.5 rounded-full bg-brand-50 text-brand-600">Sudah dinilai</span>
                            @else
                                <span class="text-xs text-slate-400">Belum dinilai</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">Belum ada rekomendasi. Generate yang pertama di atas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $riwayat->links() }}</div>
@endsection
