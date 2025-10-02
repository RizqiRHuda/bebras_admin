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
                        <a class="nav-link {{ request()->routeIs('soal_bebras.index') ? 'active' : '' }}"
                           href="{{ route('soal_bebras.index') }}">
                            <i class="bx bx-table me-1"></i> Table
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('form-soal-bebras') ? 'active' : '' }}"
                           href="{{ route('form-soal-bebras') }}">
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
                    <h5 class="card-header">Tabel Data Soal Bebras</h5>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="table-soal-bebras" 
                                   class="table table-striped table-borderless border-bottom">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Menu</th>
                                        <th>Slug</th>
                                        <th>Parent</th>
                                        <th>Judul Konten</th>
                                        {{-- <th>Body Konten</th> --}}
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
@endsection

@push('js')
<script>
$(function () {
    $('#table-soal-bebras').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('soal_bebras.index') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nama_menu', name: 'nama_menu' },
            { data: 'slug', name: 'slug' },
            { data: 'parent', name: 'parent' },
            { data: 'judul', name: 'judul' },
            { data: 'urutan', name: 'urutan' },
            {
                data: 'id', // pakai id untuk generate link
                name: 'aksi',
                orderable: false,
                searchable: false,
                render: function(id, type, row) {
                    let showUrl = "{{ route('soal_bebras.show', ':id') }}".replace(':id', id);
                    let editUrl = "{{ route('soal_bebras.edit', ':id') }}".replace(':id', id);
                    let deleteUrl = "{{ route('soal_bebras.destroy', ':id') }}".replace(':id', id);

                   return `
                        <div class="d-flex gap-1">
                            <a href="${showUrl}" class="btn btn-info btn-sm">Detail</a>
                            <a href="${editUrl}" class="btn btn-warning btn-sm">Edit</a>
                            <form action="${deleteUrl}" method="POST" onsubmit="return confirm('Yakin hapus data?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-delete">Hapus</button>
                            </form>
                        </div>
                    `;

                }
            }
        ],
        order: [[5, 'asc']] // Urutan default berdasarkan kolom Urutan
    });
});
</script>

@endpush
