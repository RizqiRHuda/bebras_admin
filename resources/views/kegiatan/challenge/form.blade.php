@extends('app')

@section('content')
    <div class="container mt-4">
        <h3 class="mb-4">Tambah Bebras Challenge</h3>
        {{-- Alert Bootstrap --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        <div class="col-md-12">
            <ul class="nav nav-pills flex-column flex-md-row mb-3">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('challenge.index') ? 'active' : '' }}"
                        href="{{ route('challenge.index') }}">
                        <i class="bx bx-table me-1"></i> Table
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('form-challenge.index') ? 'active' : '' }}"
                        href="{{ route('form-challenge.index') }}">
                        <i class="bx bx-edit me-1"></i> Form
                    </a>
                </li>
            </ul>
        </div>
        <div class="card shadow-sm border-0 rounded-3">
            <div class="card-body p-4">
                <form action="{{ route('form-challange.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tahun Event</label>
                        <input type="number" name="tahun" class="form-control" placeholder="Contoh: 2025" min="2000"
                            max="2100" value="{{ old('tahun') }}" required>
                    </div>


                    <div class="mb-3">
                        <label class="form-label fw-bold">Judul</label>
                        <input type="text" name="title" class="form-control" placeholder="Judul konten..."
                            value="{{ old('title') }}" required>
                    </div>


                    <div class="mb-3">
                        <label class="form-label fw-bold">Tagline / Hashtag</label>
                        <input type="text" name="tagline" class="form-control"
                            placeholder="#berprestasidarirumah #jujuritukeren" value="{{ old('tagline') }}">
                    </div>


                    <div class="mb-3">
                        <label class="form-label fw-bold">Cover Image</label>
                        <input type="file" name="gambar" class="form-control">
                        <small class="text-muted">Format: JPG/PNG, Max 2MB</small>
                    </div>

                    {{-- JSON TABLE --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Tabel (Opsional)</label>
                        <textarea name="table_json" rows="4" class="form-control" placeholder='Contoh: [["Level","Jumlah"],["Mudah",5]]'>{{ old('table_json') }}</textarea>
                        <small class="text-muted">Isi dalam format JSON (opsional)</small>
                    </div>

                    {{-- Content (TinyMCE) --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Konten</label>
                        <textarea id="editor" name="content" class="form-control tinymce-editor" rows="10">{{ old('content') }}</textarea>
                    </div>

                    {{-- Submit --}}
                    <div class="d-flex justify-content-end mt-4">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bx bx-save me-1"></i> Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('js')
    @endpush
@endsection
