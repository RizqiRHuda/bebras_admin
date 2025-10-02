@extends('app')

@section('content')
    <div class="content-wrapper">
        <!-- Content -->

        <div class="container-xxl flex-grow-1 container-p-y">
            <x-breadcrumbs :items="$breadcrumbs" />

            <div class="row">
                <div class="col-md-12">
                    <ul class="nav nav-pills flex-column flex-md-row mb-3">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('tentang_bebras.index') ? 'active' : '' }}"
                                href="{{ route('tentang_bebras.index') }}">
                                <i class="bx bx-table me-1"></i> Table
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('form-tentang-bebras') ? 'active' : '' }}"
                                href="{{ route('form-tentang-bebras') }}">
                                <i class="bx bx-edit me-1"></i>Form
                            </a>
                        </li>
                    </ul>

                    <div class="card">
                        <h5 class="card-header">Form</h5>
                        <div class="card-body">
                            {{-- Alert error --}}
                            @if ($errors->any())
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Terjadi kesalahan!</strong>
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif

                            @if (session('error'))
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    {{ session('error') }}
                                    <button type="button" class="btn-close" data-bs-dismiss="alert"
                                        aria-label="Close"></button>
                                </div>
                            @endif
                            <form
                                action="{{ isset($data) ? route('tentang_bebras.update', $data->id) : route('tentang_bebras.store') }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @if (isset($data))
                                    @method('PUT')
                                @endif

                                <div class="row g-3">
                                    <div class="col-md-8">
                                        <label for="judul" class="form-label">Judul</label>
                                        <input type="text" id="judul" name="judul"
                                            class="form-control @error('judul') is-invalid @enderror"
                                            value="{{ old('judul', $data->judul ?? '') }}" placeholder="Masukkan judul"
                                            required>
                                        @error('judul')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label for="urutan" class="form-label">Urutan</label>
                                        <input type="number" id="urutan" name="urutan"
                                            value="{{ old('urutan', $data->urutan ?? '-') }}"
                                            class="form-control @error('urutan') is-invalid @enderror"
                                            placeholder="Contoh: 1" required>
                                        @error('urutan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>


                                    <div class="col-md-12">
                                        <label for="slug" class="form-label">Slug</label>
                                        <input type="text" id="slug" name="slug" value="{{ old('slug') }}"
                                            class="form-control @error('slug') is-invalid @enderror"
                                            placeholder="Slug otomatis dari judul" readonly>
                                        @error('slug')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12">
                                        <label for="konten" class="form-label">Konten</label>
                                        <textarea  name="konten" class="form-control tinymce-editor @error('konten') is-invalid @enderror" rows="6">{{ old('konten', $data->konten ?? '-') }}</textarea>
                                        @error('konten')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-12">
                                        <label for="gambar" class="form-label fw-bold">Gambar</label>
                                        <div class="d-flex align-items-center p-3 border rounded shadow-sm bg-light gap-4">

                                            {{-- Input File --}}
                                            <div class="flex-grow-1">
                                                <input type="file" id="gambar" name="gambar"
                                                    class="form-control @error('gambar') is-invalid @enderror"
                                                    accept="image/*">
                                                @error('gambar')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                <small class="text-muted">Format: JPG, PNG, JPEG</small>
                                            </div>

                                            {{-- Preview Gambar --}}
                                            <div class="text-center">
                                                @if (!empty($data->gambar))
                                                    <img src="{{ asset('storage/' . $data->gambar) }}" alt="Gambar lama"
                                                        class="img-thumbnail border"
                                                        style="width: 120px; height: 120px; object-fit: cover; border-radius: 8px;">
                                                    <p class="mt-2 text-muted small">Preview</p>
                                                @else
                                                    <div class="d-flex justify-content-center align-items-center bg-white border rounded"
                                                        style="width: 120px; height: 120px;">
                                                        <i class="bx bx-image-add fs-1 text-secondary"></i>
                                                    </div>
                                                    <p class="mt-2 text-muted small">Belum ada gambar</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between mt-4">
                                        <a href="{{ route('tentang_bebras.index') }}" class="btn btn-secondary">Kembali</a>
                                        <button type="submit" class="btn btn-primary">Simpan</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>


    </div>
@endsection
@push('js')
    <script>
        document.getElementById('judul').addEventListener('keyup', function() {
            let slug = this.value.toLowerCase()
                .replace(/ /g, '-')
                .replace(/[^\w-]+/g, '');
            document.getElementById('slug').value = slug;
        });
    </script>
@endpush
