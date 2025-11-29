@extends('app')

@section('content')
    <div class="container mt-4">
        <x-breadcrumbs :items="$breadcrumbs" />
        <h3 class="mb-4">Bebras Challenge</h3>
        <div class="col-md-12">
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

            <ul class="nav nav-pills flex-column flex-md-row mb-3">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('berita.index') ? 'active' : '' }}"
                        href="{{ route('berita.index') }}">
                        <i class="bx bx-table me-1"></i> Table
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('form-berita.index') ? 'active' : '' }}"
                        href="{{ route('form-berita.index') }}">
                        <i class="bx bx-edit me-1"></i> Form
                    </a>
                </li>
            </ul>

            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body p-4">

                    <form action="{{ isset($data) ? route('berita.update', $data->id) : route('berita.store') }}"
                        method="POST" enctype="multipart/form-data">
                        @csrf
                        @if (isset($data))
                            @method('PUT')
                        @endif

                        <div class="mb-3">
                            <label for="title" class="form-label">Judul Berita</label>
                            <input type="text" name="title" id="title"
                                class="form-control @error('title') is-invalid @enderror"
                                value="{{ old('title', $data->title ?? '') }}" required>

                            @error('title')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konten</label>
                            <textarea name="konten" rows="6" class="form-control tinymce-editor @error('konten') is-invalid @enderror">
                                {{ old('konten', $data->konten ?? '') }}
                            </textarea>

                            @error('konten')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Gambar (Opsional)</label>
                            @if (isset($data->gambar))
                                <div class="mb-2">
                                    <img src="{{ asset('storage/' . $data->gambar) }}" width="100">
                                </div>
                            @endif

                            <input type="file" name="gambar" class="form-control @error('gambar') is-invalid @enderror">

                            @error('gambar')
                                <small class="text-danger">{{ $message }}</small>
                            @enderror
                        </div>

                        <div class="mt-4 text-end">
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-save me-2"></i> Simpan Berita
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
    @push('js')
    @endpush
@endsection
