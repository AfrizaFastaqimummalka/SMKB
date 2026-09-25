@extends('layouts.app')

@section('title', 'Catat Pengeluaran')

@section('content')
    <form method="POST" action="{{ route('pengeluaran.store') }}" class="max-w-md space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Tanggal</label>
            <input type="date" name="tanggal" value="{{ old('tanggal', now('Asia/Jakarta')->toDateString()) }}"
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-600">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Kategori</label>
            <select name="kategori" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-600">
                @foreach ($kategoriList as $k)
                    <option value="{{ $k }}" @selected(old('kategori') === $k)>{{ str($k)->headline() }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Jumlah (Rp)</label>
            <input type="number" name="jumlah" value="{{ old('jumlah') }}" min="1" required
                   class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-600">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Keterangan</label>
            <textarea name="keterangan" rows="2"
                      class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-600">{{ old('keterangan') }}</textarea>
        </div>
        <div class="flex gap-3">
            <button type="submit" class="rounded-lg bg-navy-950 text-white text-sm font-medium px-4 py-2 hover:bg-navy-800">Simpan</button>
            <a href="{{ route('pengeluaran.index') }}" class="text-sm text-slate-500 self-center hover:text-navy-950">Batal</a>
        </div>
    </form>
@endsection
