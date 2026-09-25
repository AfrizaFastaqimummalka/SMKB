@extends('layouts.app')

@section('title', 'Uji Akurasi')
@section('subtitle', 'Bandingkan penilaian AI dengan penilaian manual pihak keuangan')

@section('content')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/alpinejs/3.14.1/cdn.min.js" defer></script>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="rounded-2xl border border-blue-100 bg-brand-50 p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500">Total Dinilai</p>
            <p class="font-display text-2xl text-navy-950 mt-1">{{ $ringkasanAkurasi['total_dinilai'] }}</p>
        </div>
        <div class="rounded-2xl border border-blue-100 bg-brand-50 p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500">Kecocokan Label</p>
            <p class="font-display text-2xl text-navy-950 mt-1">{{ $ringkasanAkurasi['persentase_kecocokan_label'] }}%</p>
            <p class="text-xs text-slate-400 mt-0.5">{{ $ringkasanAkurasi['label_cocok'] }} dari {{ $ringkasanAkurasi['total_dinilai'] }} cocok</p>
        </div>
        <div class="rounded-2xl border border-blue-100 bg-brand-50 p-5">
            <p class="text-xs uppercase tracking-wide text-slate-500">Rata-rata Selisih Skor</p>
            <p class="font-display text-2xl text-navy-950 mt-1">{{ $ringkasanAkurasi['rata_rata_selisih_skor'] }}</p>
            <p class="text-xs text-slate-400 mt-0.5">poin (dari skala 0-100)</p>
        </div>
    </div>

    <h2 class="font-display text-lg text-navy-950 mb-3">Daftar Rekomendasi</h2>
    <div class="space-y-3">
        @forelse ($daftar as $r)
            @php
                $warna = match($r->label) {
                    'sehat' => 'text-green-700',
                    'perlu_perhatian' => 'text-amber-700',
                    default => 'text-red-700',
                };
            @endphp
            <div class="rounded-2xl border border-blue-100 p-4" x-data="{ formTerbuka: false }">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <p class="text-sm font-medium text-navy-950">{{ ucfirst($r->periode_tipe) }} · {{ $r->tanggal_mulai->format('d/m/Y') }} - {{ $r->tanggal_selesai->format('d/m/Y') }}</p>
                        <p class="text-sm mt-0.5">AI: <span class="font-medium {{ $warna }}">{{ $r->skor }}/100 — {{ \App\Models\RekomendasiKesehatan::LABEL_TEKS[$r->label] }}</span></p>
                    </div>

                    @if ($r->penilaianManual)
                        <div class="text-right">
                            <p class="text-sm">Manual: <span class="font-medium">{{ $r->penilaianManual->skor_manual }}/100 — {{ \App\Models\RekomendasiKesehatan::LABEL_TEKS[$r->penilaianManual->label_manual] }}</span></p>
                            <form method="POST" action="{{ route('uji-akurasi.destroy', $r->penilaianManual) }}" onsubmit="return confirm('Hapus penilaian manual ini?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-600 hover:underline">Hapus penilaian</button>
                            </form>
                        </div>
                    @else
                        <button type="button" @click="formTerbuka = !formTerbuka" class="text-sm text-brand-600 hover:underline">
                            <span x-text="formTerbuka ? 'Tutup form' : 'Nilai Manual'"></span>
                        </button>
                    @endif
                </div>

                @if (!$r->penilaianManual)
                    <form method="POST" action="{{ route('uji-akurasi.store', $r) }}" x-show="formTerbuka" x-cloak class="mt-4 pt-4 border-t border-slate-100 grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
                        @csrf
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Skor Manual (0-100)</label>
                            <input type="number" name="skor_manual" min="0" max="100" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Label Manual</label>
                            <select name="label_manual" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                                <option value="sehat">Sehat</option>
                                <option value="perlu_perhatian">Perlu Perhatian</option>
                                <option value="kurang_sehat">Kurang Sehat</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Dinilai Oleh</label>
                            <input type="text" name="dinilai_oleh" placeholder="Nama penilai" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm">
                        </div>
                        <div>
                            <button type="submit" class="w-full rounded-lg bg-navy-950 text-white text-sm font-medium px-4 py-2 hover:bg-navy-800">Simpan</button>
                        </div>
                        <div class="md:col-span-4">
                            <label class="block text-xs font-medium text-slate-600 mb-1">Catatan (opsional)</label>
                            <textarea name="catatan" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm" placeholder="Alasan/pertimbangan penilaian manual..."></textarea>
                        </div>
                    </form>
                @endif
            </div>
        @empty
            <p class="text-sm text-slate-400 py-8 text-center">Belum ada rekomendasi. Generate dulu di halaman Rekomendasi Kesehatan.</p>
        @endforelse
    </div>

    <div class="mt-4">{{ $daftar->links() }}</div>
@endsection
