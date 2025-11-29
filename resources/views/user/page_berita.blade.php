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
         
            <div class="card">
                <h5 class="card-header">Tabel Data Berita</h5>
                <div class="card-body">
                    <div class="table-responsive text-nowrap">
                        <table id="table_berita" class="table table-striped table-borderless border-bottom">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Judul</th>
                                    <th>Gambar</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Note</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data akan di-load via AJAX -->
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>
        <div class="content-backdrop fade"></div>
    </div>
    @endsection

    @push('js')
        <script>
            $(document).ready(function() {
                let table = $('#table_berita').DataTable({
                    processing: true,
                    serverSide: false,
                    ajax: "{{ route('data-berita.list') }}",
                   columns: [
                        { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                        { data: 'judul' },
                        { data: 'gambar' },
                        { data: 'status' },
                        { data: 'tanggal' },
                        { data: 'note' },
                        { data: 'aksi' },
                    ]
                })
            })

            function hapusData(id) {
                Swal.fire({
                    title: 'Yakin ingin menghapus?',
                    text: "Data yang dihapus tidak bisa dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, hapus!'
                }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                             url: "{{ route('berita.destroy', '') }}/" + id,
                            type: "DELETE",
                            data: {
                                _token: "{{ csrf_token() }}"
                            },
                            success: function(res) {

                                $('#table_berita').DataTable().ajax.reload();

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Berhasil!',
                                    text: 'Data berhasil dihapus',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            },
                            error: function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Oops...',
                                    text: 'Terjadi kesalahan saat menghapus data'
                                });
                            }
                        });

                    }
                })
            }
        </script>
    @endpush
