@extends('app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow border-0 rounded-4">
                    <div class="card-body p-4">
                        {{-- Header --}}
                        <div class="text-center mb-4">
                            <h4 class="fw-bold text-primary mb-1">📄 Import Hasil Challenge</h4>
                            <p class="text-muted small mb-0">Unggah file hasil challenge dalam format Excel</p>
                        </div>

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
                        
                        {{-- Form --}}
                        <form action="{{ route('pengumuman.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf

                            <div class="d-flex flex-wrap gap-3 justify-content-between">
                                {{-- Tahun --}}
                                <div class="flex-fill" style="min-width: 220px;">
                                    <label for="tahun" class="form-label fw-semibold">🗓️ Tahun</label>
                                    <input type="number" name="tahun" id="tahun" class="form-control"
                                        placeholder="2025" min="2000" max="{{ date('Y') + 1 }}" required>
                                </div>

                                {{-- Kategori --}}
                                <div class="flex-fill" style="min-width: 220px;">
                                    <label for="kategori" class="form-label fw-semibold">🏷️ Kategori</label>
                                    <select name="id_kategori" id="id_kategori" class="form-select" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach ($kategori as $k)
                                            <option value="{{ $k->id }}">{{ $k->deskripsi}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- File Upload --}}
                                <div class="flex-fill" style="min-width: 220px;">
                                    <label for="file_excel" class="form-label fw-semibold">📁 File Excel</label>
                                    <input type="file" name="file_excel" id="file_excel" class="form-control"
                                        accept=".xlsx, .xls">
                                    <small class="text-muted d-block mt-1">Format: .xlsx, .xls</small>
                                </div>
                            </div>

                            {{-- Tombol Import --}}
                            <div class="text-center mt-4">
                                <button type="submit" class="btn btn-primary px-5 py-2 rounded-3 shadow-sm">
                                    <i class="bi bi-upload me-2"></i> Import Data
                                </button>
                            </div>
                        </form>


                        {{-- Table Preview (opsional) --}}
                        <div class="table-responsive">
                <table id="tableHasil" class="table table-bordered table-sm align-middle text-center w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>ID Tahun</th>
                            <th>Tahun</th>
                            <th>Kategori</th>
                        </tr>
                    </thead>
                </table>
            </div>

                    </div>
                </div>
            </div>
        </div>
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
       $(document).ready(function() {
            $('#tableHasil').DataTable({
                processing: true,
                serverSide: true,
                ajax: '{{ route('pengumuman.data') }}',
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                    { data: 'id_tahun', name: 'h.id_tahun' },
                    { data: 'tahun', name: 't.tahun' },
                    { data: 'kategori', name: 'k.nama_kategori' },
                ],
                pageLength: 25,
                autoWidth: false,
                responsive: true,
            });
        });

        </script>
    @endpush
@endsection
