@extends('layouts.app')

@section('title', 'Tambah Pengeluaran')
@section('subtitle', 'Catat pengeluaran baru')

@section('content')
    <div style="max-width:480px;">
        <div class="card">
            <form method="POST" action="{{ route('pengeluaran.store') }}">
                @csrf

                <div class="form-group">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal"
                           value="{{ old('tanggal', now('Asia/Jakarta')->toDateString()) }}"
                           class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Kategori</label>
                    <select name="kategori" required class="form-select">
                        @foreach ($kategoriList as $k)
                            <option value="{{ $k }}" @selected(old('kategori') === $k)>
                                {{ str($k)->headline() }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Jumlah (Rp)</label>
                    <input type="number" name="jumlah" value="{{ old('jumlah') }}"
                           min="1" required placeholder="Contoh: 75000"
                           class="form-input">
                </div>

                <div class="form-group">
                    <label class="form-label">Keterangan <span style="color:#9ca3af;font-weight:400;">(opsional)</span></label>
                    <textarea name="keterangan" rows="2" placeholder="Keterangan tambahan..."
                              class="form-input" style="resize:vertical;">{{ old('keterangan') }}</textarea>
                </div>

                <div style="display:flex;align-items:center;gap:10px;margin-top:4px;">
                    <button type="submit" class="btn btn-primary">Simpan Pengeluaran</button>
                    <a href="{{ route('pengeluaran.index') }}"
                       style="font-size:13.5px;color:#6b7280;text-decoration:none;font-weight:500;"
                       onmouseover="this.style.color='#111827'" onmouseout="this.style.color='#6b7280'">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
@endsection
