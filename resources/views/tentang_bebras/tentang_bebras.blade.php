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
                        <a class="nav-link {{ request()->routeIs('tentang_bebras.index') ? 'active' : '' }}"
                           href="{{ route('tentang_bebras.index') }}">
                            <i class="bx bx-table me-1"></i> Table
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('form-tentang-bebras') ? 'active' : '' }}"
                           href="{{ route('form-tentang-bebras') }}">
                            <i class="bx bx-edit me-1"></i> Form
                        </a>
                    </li>
                </ul>

                <!-- Alert Success -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <strong>Sukses!</strong> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <!-- Alert Error -->
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <strong>Error!</strong> {{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                <div class="card">
                    <h5 class="card-header">Tabel Data Tentang Bebras</h5>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table-tentang-bebras" class="table table-striped table-borderless border-bottom">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>URL</th>
                                        <th>Judul</th>
                                        <th>Gambar</th>
                                        <th>Urutan</th>
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
@include('tentang_bebras.detail')
@endsection

@push('js')
<script>
$(document).ready(function() {
    $('#table-tentang-bebras').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('tentang_bebras.data') }}",
        columns: [
            { 
                data: 'DT_RowIndex', 
                name: 'DT_RowIndex', 
                orderable: false, 
                searchable: false
            },
            { 
                data: 'slug', 
                name: 'slug',
                render: function(data, type, row) {
                    return '<code>/' + data + '</code>';
                }
            },
            { 
                data: 'judul', 
                name: 'judul' 
            },
            { 
                data: 'gambar', 
                name: 'gambar', 
                orderable: false, 
                searchable: false,
            },
            { 
                data: 'urutan', 
                name: 'urutan',
            },
            { 
                data: 'aksi', 
                name: 'aksi', 
                orderable: false, 
                searchable: false,
            }
        ],
        order: [[4, 'asc']],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
        }
    });

 $(document).on('click', '.btn-detail', function() {
    const id = $(this).data('id');
    const judul = $(this).data('judul');
    const konten = $('#konten-' + id).html(); // ambil konten dari hidden div

    $('#detailModalTitle').text('Detail: ' + judul);
    $('#detailKonten').html(konten);
    $('#detailModal').modal('show');
});



    // Auto close alert
    setTimeout(function() {
        $('.alert').alert('close');
    }, 5000);
});
</script>
@endpush