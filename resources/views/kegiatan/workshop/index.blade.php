@extends('app')

@section('content')
    <div class="container mt-4">
        <h3 class="mb-4">Tambah Workshop</h3>
        <div class="col-md-12">
            <ul class="nav nav-pills flex-column flex-md-row mb-3">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('page_workhop.index') ? 'active' : '' }}"
                        href="{{ route('page_workhop.index') }}">
                        <i class="bx bx-table me-1"></i> Table
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('workshop.index') ? 'active' : '' }}"
                        href="{{ route('workshop.index') }}">
                        <i class="bx bx-edit me-1"></i> Form
                    </a>
                </li>
            </ul>
        </div>

        {{-- Alert Sukses --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Alert Error --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mt-3">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Error Validasi --}}
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mt-3">
                <strong>Periksa kembali input Anda:</strong>
                <ul class="mt-2">
                    @foreach ($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form action="{{ isset($data) ? route('workshop.update', $data->id) : route('workshop.store') }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @if (isset($data))
                @method('PUT')
            @endif

            <div class="card p-4 shadow-sm">

                {{-- Judul --}}
                <div class="mb-3">
                    <label class="form-label">Judul Workshop</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $data->title ?? '') }}"
                        placeholder="Masukkan judul workshop" required>
                </div>

                <div class="row">
                    <div class="col-md-6">

                        {{-- Tahun --}}
                        <div class="mb-3">
                            <label class="form-label">🗓️ Tahun</label>
                            <input type="number" name="tahun" class="form-control"
                                value="{{ old('tahun', $data->tahun ?? '') }}" placeholder="2025" min="2000"
                                max="{{ date('Y') + 1 }}" required>
                        </div>


                        {{-- Tanggal --}}
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="tanggal" class="form-control"
                                value="{{ old('tanggal', $data->tanggal ?? '') }}">
                        </div>

                    </div>

                    <div class="col-md-6">

                        {{-- Lokasi --}}
                        <div class="mb-3">
                            <label class="form-label">Lokasi</label>
                            <input type="text" name="lokasi" class="form-control"
                                value="{{ old('lokasi', $data->lokasi ?? '') }}" placeholder="Contoh: Aula Kampus ABC">
                        </div>

                        {{-- Gambar --}}
                        <div class="mb-3">
                            <label class="form-label">Gambar (Opsional)</label>
                            <input type="file" name="gambar" class="form-control" accept="image/*">

                            @if (isset($data) && $data->gambar)
                                <div class="mt-2">
                                    <strong>Gambar Saat Ini:</strong><br>
                                    <img src="{{  $data->gambar }}" alt="Preview Gambar" width="150"
                                        style="border-radius: 10px; object-fit:cover;">
                                </div>
                            @endif
                        </div>


                    </div>
                </div>

                <div class="mt-3">
                    <textarea name="konten" class="form-control tinymce-editor" rows="10">
                        {!! old('konten', $data->konten['html'] ?? '') !!}
                    </textarea>
                </div>

            </div>

            <button type="submit" class="btn btn-success mt-3">
                {{ isset($data) ? 'Update' : 'Simpan' }}
            </button>
        </form>

    </div>
    @push('js')
        <script>
            document.getElementById('file_excel').addEventListener('change', function() {
                if (this.files.length > 0) {
                    const label = this.nextElementSibling;
                    const fileName = this.files[0].name;
                    if (label && label.tagName === 'SMALL') label.textContent = "📄 " + fileName;
                }
            });
        </script>
    @endpush
@endsection
