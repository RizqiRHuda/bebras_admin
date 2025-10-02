<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formEditKontak">
                @csrf
                @method('PUT')
                <input type="hidden" id="kontak_id">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Kontak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>Nama</label>
                        <input type="text" class="form-control" name="nama" id="nama" required>
                    </div>
                    <div class="mb-3">
                        <label>Institusi</label>
                        <input type="text" class="form-control" name="institusi" id="institusi">
                    </div>
                    <div class="mb-3">
                        <label>Alamat</label>
                        <textarea class="form-control" name="alamat" id="alamat"></textarea>
                    </div>

                    <div id="detailsContainer">
                        <h6>Detail Kontak</h6>
                    </div>
                    <button type="button" class="btn btn-sm btn-primary" id="addDetail">Tambah Detail</button>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Simpan</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('js')
    <script>
        $(document).ready(function() {
            let detailIndex = 0;
            let editModal = new bootstrap.Modal(document.getElementById('editModal'));

            function addDetailField(id = '', tipe = '', nilai = '') {
                let html = `
                        <div class="row mb-2 detail-item">
                            <input type="hidden" name="detail[${detailIndex}][id]" value="${id}">
                            <div class="col-4">
                                <select name="detail[${detailIndex}][tipe]" class="form-select" required>
                                    <option value="email" ${tipe=='email'?'selected':''}>Email</option>
                                    <option value="url" ${tipe=='url'?'selected':''}>URL</option>
                                    <option value="telepon" ${tipe=='telepon'?'selected':''}>Telepon</option>
                                    <option value="fax" ${tipe=='fax'?'selected':''}>Fax</option>
                                    <option value="lainnya" ${tipe=='lainnya'?'selected':''}>Lainnya</option>
                                </select>
                            </div>
                            <div class="col-7">
                                <input type="text" name="detail[${detailIndex}][nilai]" class="form-control" value="${nilai}" required>
                            </div>
                            <div class="col-1">
                                <button type="button" class="btn btn-danger btn-sm removeDetail">&times;</button>
                            </div>
                        </div>
                    `;
                $('#detailsContainer').append(html);
                detailIndex++;
            }

            $(document).on('click', '.btn-edit', function() {
                let id = $(this).data('id');
                $.get(`/kontak/${id}/edit`, function(data) {
                    $('#kontak_id').val(data.id);
                    $('#nama').val(data.nama || '');
                    $('#institusi').val(data.institusi || '');
                    $('#alamat').val(data.alamat || '');
                    $('#detailsContainer .detail-item').remove();
                    detailIndex = 0;
                    if (data.details && data.details.length > 0) {
                        data.details.forEach(function(d) {
                            addDetailField(d.id, d.tipe, d.nilai);
                        });
                    }

                    editModal.show();
                });
            });
            $('#addDetail').click(function() {
                addDetailField();
            });

            $(document).on('click', '.removeDetail', function() {
                $(this).closest('.detail-item').remove();
            });

            $('#formEditKontak').submit(function(e) {
                e.preventDefault();
                let id = $('#kontak_id').val();

                $.ajax({
                    url: `/kontak/${id}`,
                    method: 'PUT',
                    data: $(this).serialize(),
                    success: function(res) {
                        if (res.success) {
                            editModal.hide();
                            alert(res.message);
                            $('#tableKontak').DataTable().ajax.reload();
                        } else {
                            alert(res.message);
                        }
                    },
                    error: function(xhr) {
                        let msg = xhr.responseJSON?.message || 'Terjadi kesalahan';
                        alert(msg);
                    }
                });
            });
            $(document).on('click', '.btn-hapus', function() {
                let id = $(this).data('id');

                Swal.fire({
                    title: 'Yakin hapus data?',
                    text: "Data yang sudah dihapus tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/kontak/${id}`,
                            type: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(res) {
                                if (res.success) {
                                    Swal.fire({
                                        title: 'Berhasil!',
                                        text: res.message,
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                    $('#tableKontak').DataTable().ajax.reload();
                                } else {
                                    Swal.fire('Gagal!', res.message, 'error');
                                }
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    xhr.responseJSON?.message ||
                                    'Terjadi kesalahan',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });


        });
    </script>
@endpush
