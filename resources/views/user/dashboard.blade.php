@extends('app')

@section('content')
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h1 class="h2 fw-bold text-dark mb-2">Selamat Datang, {{ auth()->user()->name }} 👋</h1>
                <p class="text-muted">Dashboard Overview - Statistik Berita</p>
            </div>
            <div class="text-end">
                <p class="text-muted mb-1">Terakhir update</p>
                <p class="text-dark fw-semibold">{{ now()->format('d M Y, H:i') }}</p>
            </div>
        </div>

        <div class="row g-4">

            <!-- Total Berita -->
            <div class="col-md-4 col-xl-3">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Total Berita</h6>
                        <h2 class="fw-bold text-dark">{{ $totalBerita }}</h2>
                        <p class="text-muted small">Semua berita yang kamu buat</p>
                    </div>
                    <div class="card-footer bg-primary border-0 rounded-bottom-4" style="height: 5px;"></div>
                </div>
            </div>

    
            <!-- Approved -->
            <div class="col-md-4 col-xl-3">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Disetujui</h6>
                        <h2 class="fw-bold text-dark">{{ $approved }}</h2>
                        <p class="text-muted small">Sudah disetujui oleh reviewer</p>
                    </div>
                    <div class="card-footer bg-success border-0 rounded-bottom-4" style="height: 5px;"></div>
                </div>
            </div>

            <!-- Published -->
            <div class="col-md-4 col-xl-3">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Dipublish</h6>
                        <h2 class="fw-bold text-dark">{{ $published }}</h2>
                        <p class="text-muted small">Dapat dilihat oleh publik</p>
                    </div>
                    <div class="card-footer bg-info border-0 rounded-bottom-4" style="height: 5px;"></div>
                </div>
            </div>

            <!-- Rejected -->
            <div class="col-md-4 col-xl-3">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-body">
                        <h6 class="text-muted mb-1">Ditolak</h6>
                        <h2 class="fw-bold text-dark">{{ $rejected }}</h2>
                        <p class="text-muted small">Perlu revisi dan submit ulang</p>
                    </div>
                    <div class="card-footer bg-danger border-0 rounded-bottom-4" style="height: 5px;"></div>
                </div>
            </div>

        </div>

        <!-- Summary Section -->
        <div class="row mt-5">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0 fw-bold">Ringkasan Status Berita</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-4 border-end py-3">
                                <h6 class="text-muted">Persentase Dipublish</h6>
                                <h2 class="fw-bold text-success">
                                    {{ $totalBerita ? round(($published / $totalBerita) * 100, 1) : 0 }}%</h2>
                                <small class="text-muted">Dari total berita</small>
                            </div>
                            <div class="col-md-4 border-end py-3">
                                <h6 class="text-muted">Tingkat Approval</h6>
                                <h2 class="fw-bold text-primary">
                                    {{ $totalBerita ? round((($approved + $published) / $totalBerita) * 100, 1) : 0 }}%</h2>
                                <small class="text-muted">Approved + Published</small>
                            </div>
                            <div class="col-md-4 py-3">
                                <h6 class="text-muted">Dalam Proses</h6>
                                <h2 class="fw-bold text-warning">
                                    {{ $totalBerita ? round((($draft + $submitted) / $totalBerita) * 100, 1) : 0 }}%</h2>
                                <small class="text-muted">Draft + Review</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="card-title mb-0 fw-bold">Status Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <div class="list-group-item border-0 px-0 d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-circle text-primary me-2"></i> Total Berita</span>
                                <span class="fw-bold">{{ $totalBerita }}</span>
                            </div>
                            <div class="list-group-item border-0 px-0 d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-circle text-success me-2"></i> Dipublish</span>
                                <span class="fw-bold">{{ $published }}</span>
                            </div>
                            <div class="list-group-item border-0 px-0 d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-circle text-warning me-2"></i> Dalam Proses</span>
                                <span class="fw-bold">{{ $draft + $submitted }}</span>
                            </div>
                            <div class="list-group-item border-0 px-0 d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-circle text-danger me-2"></i> Ditolak</span>
                                <span class="fw-bold">{{ $rejected }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        .card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border-radius: 12px;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
        }

        .display-6 {
            font-size: 2.5rem;
        }

        .progress {
            border-radius: 10px;
        }

        .list-group-item {
            padding: 0.75rem 0;
        }

        .bg-opacity-10 {
            background-color: rgba(var(--bs-primary-rgb), 0.1);
        }
    </style>
@endsection
