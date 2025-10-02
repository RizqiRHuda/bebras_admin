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
                            <a class="nav-link {{ request()->routeIs('soal_bebras.index') ? 'active' : '' }}"
                                href="{{ route('soal_bebras.index') }}">
                                <i class="bx bx-table me-1"></i> Table
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('form-soal-bebras') ? 'active' : '' }}"
                                href="{{ route('form-soal-bebras') }}">
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
                                action="{{ isset($data) ? route('soal_bebras.update', $data->id) : route('soal_bebras.store') }}"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                @if (isset($data))
                                    @method('PUT')
                                @endif

                                <div class="row g-3">

                                    <!-- Parent Menu -->
                                    <div class="col-md-6">
                                        <label for="parent_id" class="form-label">Parent Menu</label>
                                        <select id="parent_id" name="parent_id"
                                            class="form-select @error('parent_id') is-invalid @enderror">
                                            <option value="">-- Tidak Ada --</option>
                                            @foreach ($menuList as $menu)
                                                <option value="{{ $menu->id }}"
                                                    {{ old('parent_id', $data->parent_id ?? '') == $menu->id ? 'selected' : '' }}>
                                                    {{ $menu->nama_menu }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('parent_id')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Nama Menu -->
                                    <div class="col-md-6">
                                        <label for="nama_menu" class="form-label">Nama Menu</label>
                                        <input type="text" id="nama_menu" name="nama_menu"
                                            class="form-control @error('nama_menu') is-invalid @enderror"
                                            value="{{ old('nama_menu', $data->nama_menu ?? '') }}"
                                            placeholder="Masukkan nama menu" required>
                                        @error('nama_menu')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Slug -->
                                    <div class="col-md-6">
                                        <label for="slug" class="form-label">Slug</label>
                                        <input type="text" id="slug" name="slug"
                                            value="{{ old('slug', $data->slug ?? '') }}"
                                            class="form-control @error('slug') is-invalid @enderror"
                                            placeholder="Slug otomatis dari nama menu" readonly>
                                        @error('slug')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Urutan -->
                                    <div class="col-md-6">
                                        <label for="urutan" class="form-label">Urutan</label>
                                        <input type="number" id="urutan" name="urutan"
                                            value="{{ old('urutan', $data->urutan ?? 0) }}"
                                            class="form-control @error('urutan') is-invalid @enderror"
                                            placeholder="Contoh: 1">
                                        @error('urutan')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Judul Konten -->
                                    <div class="col-md-12">
                                        <label class="form-label">Judul Konten</label>
                                        <input type="text" name="judul" class="form-control"
                                            value="{{ old('judul', $data->judul ?? '') }}">
                                    </div>

                                    <!-- Body Konten -->
                                    <div class="col-md-12">
                                        <label class="form-label">Body Konten</label>
                                        <textarea name="body" class="form-control tinymce-editor">{{ old('body', $data->body ?? '') }}</textarea>
                                    </div>

                                    <!-- Gambar -->
                                    <div class="col-md-12">
                                        <label class="form-label">Gambar</label>
                                        <div class="d-flex align-items-center gap-3">
                                            <!-- Input File -->
                                            <div class="flex-grow-1">
                                                <input type="file" name="gambar" id="gambar" class="form-control"
                                                    accept="image/*">
                                                <small class="text-muted">Pilih file gambar (maks 2MB)</small>
                                            </div>

                                            <!-- Preview Gambar -->
                                            <div>
                                                @if (isset($data) && $data->gambar)
                                                    <div class="border rounded overflow-hidden"
                                                        style="width: 120px; height: 120px; display:flex; justify-content:center; align-items:center;">
                                                        <img src="{{ Storage::url($data->gambar) }}" alt="Preview Gambar"
                                                            class="img-fluid" style="max-width:100%; max-height:100%;">
                                                    </div>
                                                    <p class="mt-2 text-center text-muted small">Preview</p>
                                                @else
                                                    <div class="border rounded bg-light d-flex justify-content-center align-items-center"
                                                        style="width: 120px; height: 120px;">
                                                        <i class="bx bx-image-add fs-2 text-secondary"></i>
                                                    </div>
                                                    <p class="mt-2 text-center text-muted small">Belum ada gambar</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Tombol -->
                                    <div class="d-flex justify-content-between mt-4">
                                        <a href="{{ route('soal_bebras.index') }}" class="btn btn-secondary">Kembali</a>
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
        document.addEventListener("DOMContentLoaded", function() {
            // Slug otomatis
            document.getElementById('nama_menu').addEventListener('keyup', function() {
                let slug = this.value.toLowerCase()
                    .replace(/ /g, '-')
                    .replace(/[^\w-]+/g, '');
                document.getElementById('slug').value = slug;
            });

            // TinyMCE sudah dipanggil via push js, otomatis inisialisasi
        });
    </script>
@endpush
