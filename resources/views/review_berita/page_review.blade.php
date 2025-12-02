@extends('app')

@section('content')

<div class="container mt-4">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="fw-bold">Review Berita</h3>

       
    </div>

    {{-- Card Wrapper --}}
    <div class="card shadow-sm">
        <div class="card-body">

            <table id="tableBerita" class="table table-hover table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>No</th>
                        <th>Judul Berita</th>
                        <th>Penulis</th>
                        <th>Tanggal</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>

        </div>
    </div>

</div>

@endsection

@push('js')
<script>

let table = $('#tableBerita').DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    ajax: "{{ route('review_berita.data') }}",
    language: {
        processing: "Memuat...",
        search: "",
    },
    columns: [
        { 
            data: 'DT_RowIndex', 
            name: 'DT_RowIndex', 
            orderable: false, 
            searchable: false,
            className: "text-center"
        },
        { data: 'title', name: 'title' },
        { data: 'penulis', name: 'penulis' },
        { data: 'tanggal', name: 'tanggal', className: "text-nowrap" },
        { data: 'aksi', name: 'aksi', orderable: false, searchable: false, className: "text-center" },
    ]
});




</script>

@endpush
