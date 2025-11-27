@extends('app')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card shadow border-0 rounded-4">
                    <div class="card-header bg-primary">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 text-white">
                                <i class="bi bi-pencil-square me-2"></i>Edit Hasil Pengumuman
                            </h5>
                            <a href="{{ route('pengumuman.index') }}" class="btn btn-light btn-sm">
                                <i class="bi bi-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                    </div>
                    
                    <div class="card-body p-4">
                        {{-- Alert Bootstrap --}}
                        @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if ($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <strong>Terdapat kesalahan:</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        {{-- Info Data Saat Ini --}}
                        <div class="alert alert-info mb-4">
                            <strong><i class="bi bi-info-circle me-2"></i>Data Saat Ini:</strong>
                            <div class="mt-2">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Tahun:</strong> {{ $hasil->tahun->tahun ?? '-' }}</p>
                                        <p class="mb-1"><strong>Kategori:</strong> {{ $hasil->kategori->nama_kategori ?? '-' }}</p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="mb-1"><strong>Platform:</strong> 
                                            @if($hasil->is_uploaded)
                                                <span class="badge bg-dark">Uploaded File</span>
                                            @else
                                                <span class="badge bg-success">{{ ucfirst(str_replace('_', ' ', $hasil->platform)) }}</span>
                                            @endif
                                        </p>
                                        @if($hasil->is_uploaded && $hasil->file_path)
                                            <p class="mb-1"><strong>File:</strong> {{ basename($hasil->file_path) }}</p>
                                        @elseif($hasil->embed_url)
                                            <p class="mb-1"><strong>URL:</strong> <small>{{ Str::limit($hasil->embed_url, 50) }}</small></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form action="{{ route('pengumuman.update', $hasil->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row g-3">
                                {{-- Tahun --}}
                                <div class="col-md-6">
                                    <label for="tahun_edit" class="form-label fw-semibold">🗓️ Tahun</label>
                                    <input type="number" name="tahun_edit" id="tahun_edit" class="form-control"
                                        value="{{ old('tahun_edit', $hasil->tahun->tahun ?? '') }}"
                                        placeholder="2025" min="2000" max="{{ date('Y') + 1 }}" required>
                                </div>

                                {{-- Kategori --}}
                                <div class="col-md-6">
                                    <label for="id_kategori_edit" class="form-label fw-semibold">🏷️ Kategori</label>
                                    <select name="id_kategori_edit" id="id_kategori_edit" class="form-select" required>
                                        <option value="">-- Pilih Kategori --</option>
                                        @foreach ($kategori as $k)
                                            <option value="{{ $k->id }}" {{ old('id_kategori_edit', $hasil->id_kategori) == $k->id ? 'selected' : '' }}>
                                                {{ $k->deskripsi }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Pilihan Update Type --}}
                                <div class="col-12">
                                    <label class="form-label fw-semibold">Pilih Jenis Update:</label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="update_type" id="update_file" value="file" autocomplete="off">
                                        <label class="btn btn-outline-primary" for="update_file">
                                            <i class="bi bi-cloud-upload me-2"></i>Upload File Baru
                                        </label>
                                        
                                        <input type="radio" class="btn-check" name="update_type" id="update_embed" value="embed" autocomplete="off">
                                        <label class="btn btn-outline-success" for="update_embed">
                                            <i class="bi bi-link-45deg me-2"></i>Ubah ke Link Embed
                                        </label>

                                        <input type="radio" class="btn-check" name="update_type" id="update_only_info" value="info" autocomplete="off" checked>
                                        <label class="btn btn-outline-secondary" for="update_only_info">
                                            <i class="bi bi-pencil me-2"></i>Hanya Ubah Info
                                        </label>
                                    </div>
                                    <small class="text-muted d-block mt-2">
                                        <i class="bi bi-info-circle"></i> 
                                        Pilih "Hanya Ubah Info" jika hanya ingin mengubah tahun/kategori/deskripsi tanpa mengganti file/URL
                                    </small>
                                </div>

                                {{-- Form Upload File --}}
                                <div class="col-12 d-none" id="file_edit_section">
                                    <label for="excel_file_edit" class="form-label fw-semibold">📁 File Excel Baru</label>
                                    <input type="file" name="excel_file_edit" id="excel_file_edit" class="form-control" 
                                        accept=".xlsx,.xls,.csv">
                                    <small class="text-muted d-block mt-1">
                                        <i class="bi bi-info-circle"></i> 
                                        Format: .xlsx, .xls, .csv (Max: 10MB)
                                        @if($hasil->is_uploaded)
                                            <br><strong>File saat ini akan diganti dengan file baru yang diupload</strong>
                                        @endif
                                    </small>
                                </div>

                                {{-- Form Embed URL --}}
                                <div class="col-12 d-none" id="embed_edit_section">
                                    <label for="embed_url_edit" class="form-label fw-semibold">🔗 Link Embed Spreadsheet</label>
                                    <input type="url" name="embed_url_edit" id="embed_url_edit" class="form-control"
                                        value="{{ old('embed_url_edit', $hasil->embed_url ?? '') }}"
                                        placeholder="https://docs.google.com/spreadsheets/d/...">
                                    <small class="text-muted d-block mt-1">
                                        <i class="bi bi-info-circle"></i> 
                                        Hanya Google Sheets yang support embed langsung
                                        @if($hasil->is_uploaded)
                                            <br><strong>File saat ini akan dihapus dan diganti dengan link embed</strong>
                                        @endif
                                    </small>
                                </div>

                                {{-- Deskripsi --}}
                                <div class="col-12">
                                    <label for="description_edit" class="form-label fw-semibold">📝 Deskripsi (Opsional)</label>
                                    <textarea name="description_edit" id="description_edit" class="form-control" rows="3"
                                        placeholder="Contoh: Hasil Challenge Bebras 2025 Kategori Siaga">{{ old('description_edit', $hasil->description) }}</textarea>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <a href="{{ route('pengumuman.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Batal
                                </a>
                                <button type="submit" class="btn btn-warning text-white px-4">
                                    <i class="bi bi-save me-2"></i>Update Data
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('js')
        <script>
            $(document).ready(function() {
                // Handle update type selection
                $('input[name="update_type"]').on('change', function() {
                    const type = $(this).val();
                    
                    // Hide all sections first
                    $('#file_edit_section, #embed_edit_section').addClass('d-none');
                    $('#excel_file_edit').removeAttr('required');
                    $('#embed_url_edit').removeAttr('required');
                    
                    if (type === 'file') {
                        $('#file_edit_section').removeClass('d-none');
                        $('#excel_file_edit').attr('required', true);
                    } else if (type === 'embed') {
                        $('#embed_edit_section').removeClass('d-none');
                        $('#embed_url_edit').attr('required', true);
                    }
                    // If 'info', nothing to show
                });

                // Validation before submit
                $('form').on('submit', function(e) {
                    const updateType = $('input[name="update_type"]:checked').val();
                    
                    if (updateType === 'file' && !$('#excel_file_edit').val()) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Peringatan!',
                            text: 'Silakan pilih file untuk diupload'
                        });
                        return false;
                    }
                    
                    if (updateType === 'embed' && !$('#embed_url_edit').val()) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Peringatan!',
                            text: 'Silakan masukkan URL embed'
                        });
                        return false;
                    }

                    // Confirmation
                    e.preventDefault();
                    Swal.fire({
                        title: 'Konfirmasi Update',
                        text: "Apakah Anda yakin ingin mengupdate data ini?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#ffc107',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya, Update!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $(this).off('submit').submit();
                        }
                    });
                });
            });
        </script>
    @endpush
@endsection
