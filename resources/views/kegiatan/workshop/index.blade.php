@extends('app')

@section('content')
<div class="container mt-4">
    <h3 class="mb-4">Tambah Workshop</h3>

    {{-- <form action="{{ route('workshop.store') }}" method="POST" enctype="multipart/form-data" id="formWorkshop"> --}}
        <from action="#">
        @csrf

        <div class="card p-4 shadow-sm">
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Tahun</label>
                    <select name="id_tahun" class="form-select">
                       
                    </select>
                </div>
            </div>

            <!-- Judul -->
            <div class="mb-3">
                <label class="form-label fw-bold">Judul Workshop</label>
                <input type="text" class="form-control" name="judul" placeholder="Judul workshop...">
            </div>

            <!-- Gambar -->
            <div class="mb-3">
                <label class="form-label fw-bold">Gambar</label>
                <input type="file" class="form-control" name="gambar">
            </div>

            <!-- Dynamic Content Builder -->
            <div class="mb-3">
                <label class="form-label fw-bold">Konten Workshop</label>

                <div id="kontenWrapper"></div>

                <button type="button" class="btn btn-outline-primary mt-3" id="btnTambahKonten">
                    + Tambah Bagian Konten
                </button>

                <textarea name="konten" id="kontenJSON" hidden></textarea>
            </div>

            <button type="submit" class="btn btn-success mt-3">Simpan</button>
        </div>
    </form>
</div>

@push('js')
<script>
let index = 0;

document.getElementById('btnTambahKonten').addEventListener('click', () => {
    const wrapper = document.getElementById('kontenWrapper');

    wrapper.insertAdjacentHTML('beforeend', `
        <div class="card p-3 mt-3 shadow-sm konten-item">
            <label class="form-label fw-bold">Jenis Bagian</label>
            <select class="form-select jenis" data-index="${index}">
                <option value="text">Paragraf</option>
                <option value="title">Judul Kecil</option>
                <option value="list">List</option>
            </select>

            <div class="konten-field mt-2">
                <textarea class="form-control value" placeholder="Tulis konten..."></textarea>
            </div>

            <button type="button" class="btn btn-danger btn-sm mt-2 btnHapus">Hapus</button>
        </div>
    `);

    index++;
});

// hapus
document.addEventListener('click', function(e){
    if(e.target.classList.contains('btnHapus')){
        e.target.closest('.konten-item').remove();
    }
});

// sebelum submit → generate JSON
document.getElementById('formWorkshop').addEventListener('submit', function(e){
    const items = document.querySelectorAll('.konten-item');
    let data = [];

    items.forEach(item => {
        data.push({
            jenis: item.querySelector('.jenis').value,
            isi: item.querySelector('.value').value
        });
    });

    document.getElementById('kontenJSON').value = JSON.stringify(data);
});
</script>
@endpush
@endsection
