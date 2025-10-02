@extends('app')

@section('content')
    <div class="card">
        <h5 class="card-header">Kontak</h5>
        <div class="card-body">
            <div class="col-md p-2 d-flex justify-content-end">
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#kontakModal">+ Tambah</button>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-bordered" id="table-latihan">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Deskripsi</th>
                            <th>Link</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- data akan di-load via JS/AJAX atau blade loop -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="kontakModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Form Latihan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="formLatihan" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nama</label>
                                    <input type="text" name="nama" class="form-control"
                                        placeholder="Masukkan nama latihan">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Link</label>
                                    <input type="text" name="link" class="form-control"
                                        placeholder="Masukkan link terkait">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control" rows="3" placeholder="Tambahkan deskripsi..."></textarea>
                        </div>


                        <div class="mb-3">
                            <label class="form-label">Gambar</label>
                            <input type="file" name="gambar" class="form-control">
                        </div>
                    </form>
                </div>

                {{-- Footer --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btnSimpan">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="formEditLatihan" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" id="edit_id">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Latihan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label>Nama</label>
                            <input type="text" class="form-control" id="edit_nama" name="nama">
                        </div>
                        <div class="mb-3">
                            <label>Deskripsi</label>
                            <textarea class="form-control" id="edit_deskripsi" name="deskripsi"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Link</label>
                            <input type="text" class="form-control" id="edit_link" name="link">
                        </div>

                        <div class="row">
                            <!-- Gambar Lama -->
                            <div class="col-md-6">
                                <div class="card shadow-sm h-100">
                                    <div class="card-header bg-light">
                                        <strong>Gambar Lama</strong>
                                    </div>
                                    <div class="card-body text-center">
                                        <img id="preview_gambar" src="" class="img-fluid rounded mb-2 border"
                                            style="max-height:100px; display:none;">
                                        <p class="text-muted small" id="no_image_text">Belum ada gambar</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Upload Baru -->
                            <div class="col-md-6">
                                <div class="card shadow-sm h-100">
                                    <div class="card-header bg-light">
                                        <strong>Ganti / Tambah Gambar</strong>
                                    </div>
                                    <div class="card-body">
                                        <input type="file" class="form-control" id="edit_gambar" name="gambar"
                                            accept="image/*">
                                        <div class="mt-3 text-center">
                                            <img id="preview_new" class="img-fluid rounded border"
                                                style="max-height:200px; display:none;" />
                                            <p class="text-muted small">Preview gambar baru</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Detail -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content shadow-lg rounded-3 border-0">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-info-circle "></i> Detail Latihan
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close">
                    </button>
                </div>

                <div class="modal-body p-4">
                    <!-- Nama -->
                    <h4 id="detail_nama" class="fw-bold text-dark mb-3"></h4>

                    <!-- Deskripsi -->
                    <div class="mb-3">
                        <p class="fw-semibold mb-1 text-secondary">
                            <i class="bi bi-card-text me-2"></i> Deskripsi
                        </p>
                        <div class="p-3 bg-light rounded shadow-sm border" id="detail_deskripsi"></div>
                    </div>
                </div>

                <div class="modal-footer border-0 d-flex justify-content-end">
                    <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i> Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>



    @include('latihan.script')
@endsection
