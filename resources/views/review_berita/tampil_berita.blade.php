@extends('app')

@section('content')
    <div class="container py-4">

        {{-- Card Judul --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">
                <h3 class="fw-bold text-primary mb-1">{{ $berita->title }}</h3>
                <small class="text-muted">
                    Ditulis oleh <b>{{ $berita->user->name }}</b> •
                    {{ $berita->created_at->format('d M Y H:i') }}
                </small>
            </div>
        </div>

        {{-- Konten --}}
        <div class="card shadow-sm border-0 mb-4">
            @if ($berita->gambar)
                <div class="mb-4 text-center mt-2">
                    <img src="{{ $berita->gambar }}" class="img-fluid rounded shadow-sm"
                        style="max-height: 250px; object-fit: cover;">
                </div>
            @endif
            <div class="card-body" style="line-height: 1.8; font-size: 1rem;">
                {!! $berita->konten !!} {{-- HTML aman & ter-render rapi --}}
            </div>
        </div>

        {{-- Section Riwayat Review --}}
        @if ($berita->review->count() > 0)
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-light fw-bold">Riwayat Review</div>

                <div class="card-body">
                    @foreach ($berita->review as $rev)
                        <div class="p-3 mb-3 border rounded bg-white">
                            <div class="d-flex justify-content-between">
                                <span>
                                    <b>{{ $rev->reviewer->name }}</b> —
                                    <span
                                        class="badge 
                                        {{ $rev->status == 'approved' ? 'bg-success' : 'bg-danger' }}">
                                        {{ ucfirst($rev->status) }}
                                    </span>
                                </span>

                                <small class="text-muted">
                                    {{ $rev->created_at->format('d M Y H:i') }}
                                </small>
                            </div>

                            @if ($rev->note)
                                <p class="mt-2 mb-0 text-secondary">{{ $rev->note }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        <form action="{{ route('review_berita.submit', $berita->id) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label class="form-label fw-bold">Catatan Reviewer</label>
                <textarea name="note" class="form-control" rows="4" placeholder="Tulis catatan tambahan..."></textarea>
            </div>

            <div class="d-flex justify-content-between align-items-center">

                <!-- Tombol Setujui & Tolak -->
                <div class="d-flex gap-2">
                    <button type="submit" name="status" value="approved" class="btn btn-success px-4">
                        ✔ Setujui
                    </button>

                    <button type="submit" name="status" value="rejected" class="btn btn-danger px-4">
                        ✘ Tolak
                    </button>
                </div>

                <!-- Tombol Kembali di pojok kanan -->
                <a href="{{ url()->previous() }}" class="btn btn-secondary px-4">
                    Kembali
                </a>

            </div>
        </form>

    </div>
    </div>
@endsection

@push('js')
    <script>
        // Tambahkan JS jika diperlukan nanti
    </script>
@endpush
