@extends('layouts.app')

@section('title', 'Pemasukan')

@section('content')
    <div class="flex items-center justify-between mb-5 gap-3 flex-wrap">
        <form method="GET" class="flex flex-wrap gap-2 text-sm">
            <input type="date" name="dari" value="{{ request('dari') }}" class="rounded-lg border border-slate-300 px-3 py-2">
            <input type="date" name="sampai" value="{{ request('sampai') }}" class="rounded-lg border border-slate-300 px-3 py-2">
            <select name="kategori" class="rounded-lg border border-slate-300 px-3 py-2">
                <option value="">Semua kategori</option>
                @foreach ($kategoriList as $k)
                    <option value="{{ $k }}" @selected(request('kategori') === $k)>{{ str($k)->headline() }}</option>
                @endforeach
            </select>
            <button type="submit" class="rounded-lg border border-slate-300 px-3 py-2 hover:border-navy-950">Filter</button>
        </form>
        <a href="{{ route('pemasukan.create') }}" class="rounded-lg bg-navy-950 text-white text-sm font-medium px-4 py-2 hover:bg-navy-800 whitespace-nowrap">
            + Catat Pemasukan
        </a>
    </div>

    <div class="rounded-2xl border border-blue-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-brand-50 text-slate-500 text-left">
                <tr>
                    <th class="px-4 py-3 font-medium">Tanggal</th>
                    <th class="px-4 py-3 font-medium">Keterangan</th>
                    <th class="px-4 py-3 font-medium">Kategori</th>
                    <th class="px-4 py-3 font-medium text-right">Jumlah</th>
                    <th class="px-4 py-3 font-medium text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($pemasukan as $p)
                    <tr>
                        <td class="px-4 py-3 text-slate-600">{{ $p->tanggal->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-slate-700">{{ $p->keterangan ?? '—' }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs px-2 py-0.5 rounded-full bg-brand-50 text-brand-600">{{ str($p->kategori)->headline() }}</span>
                        </td>
                        <td class="px-4 py-3 text-right font-medium text-green-700">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('pemasukan.destroy', $p) }}" onsubmit="return confirm('Hapus pemasukan ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">Belum ada data pemasukan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $pemasukan->links() }}</div>
@endsection
