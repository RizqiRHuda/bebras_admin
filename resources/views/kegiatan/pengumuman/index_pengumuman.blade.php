@extends('app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="card shadow border-0 rounded-4">
                    <div class="card-body p-4">
                        {{-- Header --}}
                        <div class="text-center mb-4">
                            <h4 class="fw-bold text-primary mb-1">📄 Kelola Hasil Challenge</h4>
                            <p class="text-muted small mb-0">Upload file Excel atau gunakan link embed spreadsheet</p>
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

                        {{-- Tabs --}}
                        <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="upload-tab" data-bs-toggle="tab" data-bs-target="#upload" type="button">
                                    <i class="bi bi-cloud-upload me-2"></i>Upload File Excel
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="embed-tab" data-bs-toggle="tab" data-bs-target="#embed" type="button">
                                    <i class="bi bi-link-45deg me-2"></i>Link Embed
                                </button>
                            </li>
                        </ul>

                        <div class="tab-content" id="myTabContent">
                            {{-- Tab Upload File --}}
                            <div class="tab-pane fade show active" id="upload" role="tabpanel">
                                <form action="{{ route('pengumuman.store') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="tahun_upload" class="form-label fw-semibold">🗓️ Tahun</label>
                                            <input type="number" name="tahun" id="tahun_upload" class="form-control"
                                                placeholder="2025" min="2000" max="{{ date('Y') + 1 }}" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="id_kategori_upload" class="form-label fw-semibold">🏷️ Kategori</label>
                                            <select name="id_kategori" id="id_kategori_upload" class="form-select" required>
                                                <option value="">-- Pilih Kategori --</option>
                                                @foreach ($kategori as $k)
                                                    <option value="{{ $k->id }}">{{ $k->deskripsi}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-12">
                                            <label for="excel_file" class="form-label fw-semibold">📁 File Excel</label>
                                            <input type="file" name="excel_file" id="excel_file" class="form-control" 
                                                accept=".xlsx,.xls,.csv" required>
                                            <small class="text-muted d-block mt-1">
                                                <i class="bi bi-info-circle"></i> 
                                                Format: .xlsx, .xls, .csv (Max: 10MB)
                                            </small>
                                        </div>

                                        <div class="col-12">
                                            <label for="description_upload" class="form-label fw-semibold">📝 Deskripsi (Opsional)</label>
                                            <textarea name="description" id="description_upload" class="form-control" rows="2"
                                                placeholder="Contoh: Hasil Challenge Bebras 2025 Kategori Siaga"></textarea>
                                        </div>
                                    </div>

                                    <div class="text-center mt-4">
                                        <button type="submit" class="btn btn-primary px-5 py-2 rounded-3 shadow-sm">
                                            <i class="bi bi-cloud-upload me-2"></i> Upload File
                                        </button>
                                    </div>
                                </form>
                            </div>

                            {{-- Tab Embed Link --}}
                            <div class="tab-pane fade" id="embed" role="tabpanel">
                                <form action="{{ route('pengumuman.store') }}" method="POST">
                                    @csrf
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="tahun_embed" class="form-label fw-semibold">🗓️ Tahun</label>
                                            <input type="number" name="tahun" id="tahun_embed" class="form-control"
                                                placeholder="2025" min="2000" max="{{ date('Y') + 1 }}" required>
                                        </div>

                                        <div class="col-md-6">
                                            <label for="id_kategori_embed" class="form-label fw-semibold">🏷️ Kategori</label>
                                            <select name="id_kategori" id="id_kategori_embed" class="form-select" required>
                                                <option value="">-- Pilih Kategori --</option>
                                                @foreach ($kategori as $k)
                                                    <option value="{{ $k->id }}">{{ $k->deskripsi}}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="col-12">
                                            <label for="embed_url" class="form-label fw-semibold">🔗 Link Embed Spreadsheet</label>
                                            <input type="url" name="embed_url" id="embed_url" class="form-control"
                                                placeholder="https://docs.google.com/spreadsheets/d/..." required>
                                            <small class="text-muted d-block mt-1">
                                                <i class="bi bi-info-circle"></i> 
                                                Hanya Google Sheets yang support embed langsung
                                            </small>
                                        </div>

                                        <div class="col-12">
                                            <label for="description_embed" class="form-label fw-semibold">📝 Deskripsi (Opsional)</label>
                                            <textarea name="description" id="description_embed" class="form-control" rows="2"
                                                placeholder="Contoh: Hasil Challenge Bebras 2025 Kategori Siaga"></textarea>
                                        </div>
                                    </div>

                                    <div class="alert alert-info mt-3">
                                        <strong>📌 Cara mendapatkan link Google Sheets:</strong><br>
                                        1. Buka Google Sheets<br>
                                        2. File → Share → "Anyone with the link can view"<br>
                                        3. Copy link dan paste di form
                                    </div>

                                    <div class="text-center mt-4">
                                        <button type="submit" class="btn btn-success px-5 py-2 rounded-3 shadow-sm">
                                            <i class="bi bi-save me-2"></i> Simpan Link Embed
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>


                        {{-- Table Preview --}}
                        <div class="table-responsive mt-5">
                            <table id="tableHasil" class="table table-bordered table-sm align-middle text-center w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Tahun</th>
                                        <th>Kategori</th>
                                        <th>Platform</th>
                                        <th>Deskripsi</th>
                                        <th>Aksi</th>
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
            $(document).ready(function() {
                const table = $('#tableHasil').DataTable({
                    processing: true,
                    serverSide: true,
                    ajax: '{{ route('pengumuman.data') }}',
                    columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                        { data: 'tahun', name: 't.tahun' },
                        { data: 'kategori', name: 'k.nama_kategori' },
                        { data: 'platform', name: 'h.platform', orderable: false },
                        { data: 'description', name: 'h.description', orderable: false },
                        { data: 'action', name: 'action', orderable: false, searchable: false },
                    ],
                    pageLength: 25,
                    autoWidth: false,
                    responsive: true,
                    order: [[1, 'desc']],
                });

                // Handle delete with SweetAlert2
                $(document).on('click', '.btn-delete', function(e) {
                    e.preventDefault();
                    const id = $(this).data('id');
                    
                    Swal.fire({
                        title: 'Konfirmasi Hapus',
                        text: "Data yang dihapus tidak dapat dikembalikan!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Hapus!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: '{{ route("pengumuman.destroy", ":id") }}'.replace(':id', id),
                                type: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    _method: 'DELETE'
                                },
                                success: function(response) {
                                    if (response.success) {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Berhasil!',
                                            text: response.message,
                                            timer: 2000,
                                            showConfirmButton: false
                                        });
                                        table.ajax.reload(null, false);
                                    } else {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Gagal!',
                                            text: response.message
                                        });
                                    }
                                },
                                error: function(xhr) {
                                    console.error('Delete error:', xhr);
                                    let errorMsg = 'Gagal menghapus data!';
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        errorMsg = xhr.responseJSON.message;
                                    }
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error!',
                                        text: errorMsg
                                    });
                                }
                            });
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
