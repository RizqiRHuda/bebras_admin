@extends('app')

@section('content')
    <div class="card">
        <h5 class="card-header">Kontak</h5>
        <div class="card-body">
            <div class="col-md p-2 d-flex justify-content-end">
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#kontakModal">+ Tambah</button>
            </div>

            <div class="table-responsive text-nowrap">
                <table class="table table-bordered" id="tableKontak">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Institusi</th>
                            <th>Alamat</th>
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
                    <h5 class="modal-title">Form Kontak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="formKontak" method="POST" action="{{ route('kontak.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="nama" class="form-label">Nama</label>
                                    <input type="text" class="form-control" id="nama" name="nama" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="institusi" class="form-label">Institusi</label>
                                    <input type="text" class="form-control" id="institusi" name="institusi">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea class="form-control" id="alamat" name="alamat"></textarea>
                        </div>
                        <div id="detail-kontak">
                            <div class="row detail-item mb-2">
                                <div class="col-md-4">
                                    <select class="form-select" name="detail[0][tipe]" required>
                                        <option value="email">Email</option>
                                        <option value="url">URL</option>
                                        <option value="telepon">Telepon</option>
                                        <option value="fax">Fax</option>
                                        <option value="lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="detail[0][nilai]"
                                            placeholder="Isi kontak">
                                        <button type="button" class="btn btn-success btn-add">+</button>
                                        <button type="button" class="btn btn-danger btn-remove">-</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="btnSimpanKontak">Simpan</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detail Kontak</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <ul id="detailList" class="list-group">
                        <!-- Detail akan di-load via AJAX -->
                    </ul>
                </div>
            </div>
        </div>
    </div>
    @endsection
    @include('kontak.script')

@push('js')
    <script>
        $(document).ready(function() {

            $('#btnSimpanKontak').on('click', function(e) {
                e.preventDefault();

                let form = $('#formKontak');
                let url = form.attr('action');

                $.ajax({
                    url: url,
                    method: 'POST',
                    data: form.serialize(),
                    success: function(res) {
                        if (res.success) {
                            // reset form
                            form[0].reset();

                            // tutup modal
                            let modal = bootstrap.Modal.getInstance(document.getElementById(
                                'kontakModal'));
                            modal.hide();

                            // tampilkan pesan sukses
                            alert(res.message);

                            // reload datatable atau list kontak jika ada
                            if (typeof reloadKontakTable !== 'undefined') {
                                reloadKontakTable();
                            }
                        } else {
                            alert(res.message);
                        }
                    },
                    error: function(xhr) {
                        let err = xhr.responseJSON;
                        if (err && err.errors) {
                            let messages = [];
                            $.each(err.errors, function(k, v) {
                                messages.push(v.join(', '));
                            });
                            alert(messages.join("\n"));
                        } else {
                            alert('Terjadi kesalahan, coba lagi.');
                        }
                    }
                });
            });

            let table = $('#tableKontak').DataTable({
                ajax: "{{ route('kontak.list') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'institusi',
                        name: 'institusi'
                    },
                    {
                        data: 'alamat',
                        name: 'alamat'
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ]
            });

            window.reloadKontakTable = function() {
                table.ajax.reload();
            }
        });

        $(document).on('click', '.btn-detail', function() {
            let kontakId = $(this).data('id');

            $.ajax({
                url: '/kontak/detail/' + kontakId, // route untuk ambil detail
                method: 'GET',
                success: function(res) {
                    let list = $('#detailList');
                    list.empty(); // kosongkan dulu

                    if (res.details.length > 0) {
                        res.details.forEach(function(d) {
                            list.append('<li class="list-group-item"><strong>' + d.tipe +
                                ':</strong> ' + d.nilai + '</li>');
                        });
                    } else {
                        list.append('<li class="list-group-item">Tidak ada detail</li>');
                    }

                    // tampilkan modal
                    let modal = new bootstrap.Modal(document.getElementById('detailModal'));
                    modal.show();
                },
                error: function() {
                    alert('Gagal memuat detail kontak');
                }
            });
        });
        let index = 1;

        // Tambah baris
        $(document).on('click', '.btn-add', function() {
            let newRow = `
                    <div class="row detail-item mb-2">
                        <div class="col-md-4">
                            <select class="form-select" name="detail[${index}][tipe]" required>
                                <option value="email">Email</option>
                                <option value="url">URL</option>
                                <option value="telepon">Telepon</option>
                                <option value="fax">Fax</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input type="text" class="form-control" name="detail[${index}][nilai]" placeholder="Isi kontak">
                                <button type="button" class="btn btn-success btn-add">+</button>
                                <button type="button" class="btn btn-danger btn-remove">-</button>
                            </div>
                        </div>
                    </div>
    `;
            $('#detail-kontak').append(newRow);
            index++;
        });

        // Hapus baris
        $(document).on('click', '.btn-remove', function() {
            $(this).closest('.detail-item').remove();
        });
    </script>
@endpush
