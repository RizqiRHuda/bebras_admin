@extends('app')

@section('content')
    <div class="content-wrapper">
        <!-- Content -->
        <div class="container-xxl flex-grow-1 container-p-y">
            <x-breadcrumbs :items="$breadcrumbs" />

            <div class="row">
                <div class="col-md-12">
                    <ul class="nav nav-pills flex-column flex-md-row mb-3">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('page_workhop.index') ? 'active' : '' }}"
                                href="{{ route('page_workhop.index') }}">
                                <i class="bx bx-table me-1"></i> Table
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('workshop.index') ? 'active' : '' }}"
                                href="{{ route('workshop.index') }}">
                                <i class="bx bx-edit me-1"></i> Form
                            </a>
                        </li>
                    </ul>

                    <!-- Alert Success -->
                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Sukses!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Alert Error -->
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> {{ $errors->first() }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="card">
                        <h5 class="card-header">Tabel Data Workshop</h5>
                        <div class="card-body">
                               <div class="table-responsive text-nowrap">
                                <table id="tableWorkshop" class="table table-striped table-borderless border-bottom">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Tanggal</th>
                                            <th>Lokasi</th>
                                            <th>Gambar</th>
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
            </div>
        </div>
        <div class="content-backdrop fade"></div>
    </div>
@endsection

@push('js')
    <script>
        $(document).ready(function() {
            let table = $('#tableWorkshop').DataTable({
                processing: true,
                serverSide: false, 
                ajax: "{{ route('workshop.list') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },   
                    { data: 'tanggal', name: 'tanggal' },    
                    { data: 'lokasi', name: 'lokasi' },
                    {data: 'gambar', name: 'gambar'},
                    { data: 'aksi', name: 'aksi', orderable: false, searchable: false }
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
                        url: "{{ url('kegiatan') }}/" + id,
                        type: "DELETE",
                        data: {
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(res) {

                            $('#tableWorkshop').DataTable().ajax.reload();

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
