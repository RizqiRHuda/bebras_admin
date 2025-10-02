@extends('app')

@section('content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <x-breadcrumbs :items="$breadcrumbs" />

            <div class="card shadow-sm border-0">
                <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center border mb-2">
                    <h5 class="mb-0 text-primary fw-bold d-flex align-items-center">
                        <i class="bx bx-book me-2"></i> Detail Menu Soal
                    </h5>
                    <div class="d-flex gap-2 mt-2 mt-md-0">
                        <a href="{{ route('soal_bebras.index') }}"
                            class="btn btn-outline-secondary btn-sm d-flex align-items-center">
                            <i class="bx bx-arrow-back me-1"></i> Kembali
                        </a>
                        <a href="{{ route('soal_bebras.edit', $menu->id) }}"
                            class="btn btn-warning btn-sm d-flex align-items-center">
                            <i class="bx bx-edit me-1"></i> Edit
                        </a>
                    </div>
                </div>
                <div class="card-body">

                    <!-- Info Menu -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p><strong>Nama Menu:</strong> {{ $menu->nama_menu }}</p>
                            <p><strong>Slug:</strong> <code>{{ $menu->slug }}</code></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Urutan:</strong> {{ $menu->urutan }}</p>
                            <p><strong>Parent:</strong> {{ $menu->parent ? $menu->parent->nama_menu : '-' }}</p>
                        </div>
                    </div>

                    <hr>

                    <!-- Konten Menu -->
                    <h6 class="fw-bold mb-3">Konten Menu</h6>
                    <div class="mb-3">
                        @if ($menu->judul)
                            <p><strong>Judul Konten:</strong> {{ $menu->judul }}</p>
                        @endif
                        @if ($menu->body)
                            <div class="konten-body mb-3">
                                {!! $menu->body !!} {{-- render HTML dari TinyMCE --}}
                            </div>
                        @endif
                        @if ($menu->gambar)
                            <div class="text-start">
                                <img src="{{ Storage::url($menu->gambar) }}" alt="Gambar Konten"
                                    class="img-fluid rounded shadow-sm" style="max-height: 150px; object-fit: contain;">
                            </div>
                        @endif

                        @if (!$menu->judul && !$menu->body && !$menu->gambar)
                            <p><em>Belum ada konten untuk menu ini.</em></p>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
