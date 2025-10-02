 @push('js')
     <script>
         $(document).ready(function() {
             // Klik tombol simpan
             $('#btnSimpan').on('click', function(e) {
                 e.preventDefault();

                 let form = $('#formLatihan')[0];
                 let formData = new FormData(form);

                 $.ajax({
                     url: "{{ route('latihan.store') }}", // route store
                     type: "POST",
                     data: formData,
                     processData: false, // jangan proses data jadi query string
                     contentType: false, // jangan set content-type default
                     success: function(res) {
                         if (res.status === 'success') {
                             // tutup modal
                             $('#kontakModal').modal('hide');

                             // reset form
                             $('#formLatihan')[0].reset();

                             // tampilkan notifikasi sederhana
                             alert(res.message);

                             // TODO: kalau ada datatable, bisa reload di sini
                             $('#table-latihan').DataTable().ajax.reload();
                         } else {
                             alert("Terjadi kesalahan!");
                         }
                     },
                     error: function(err) {
                         let msg = "Gagal menyimpan!";
                         if (err.responseJSON && err.responseJSON.message) {
                             msg = err.responseJSON.message;
                         }
                         alert(msg);
                     }
                 });
             });

             let table = $('#table-latihan').DataTable({
                 processing: true,
                 serverSide: true,
                 ajax: "{{ route('latihan.list') }}",
                 columns: [{
                         data: 'DT_RowIndex',
                         name: 'DT_RowIndex',
                         orderable: false,
                         searchable: false
                     },
                     {
                         data: 'nama',
                         name: 'nama'
                     },
                     {
                         data: 'link',
                         name: 'link'
                     },
                     {
                         data: 'gambar',
                         name: 'gambar',
                         orderable: false,
                         searchable: false
                     },
                     {
                         data: 'actions',
                         name: 'actions',
                         orderable: false,
                         searchable: false
                     }
                 ]
             });

             // Tombol Detail
             $(document).on("click", ".btn-detail", function() {
                 let id = $(this).data("id");

                 $.get("/latihan/" + id + "/deskripsi", function(res) {
                     $("#detail_nama").text(res.nama);
                     $("#detail_deskripsi").text(res.deskripsi || "-");

                   

                     $("#detailModal").modal("show");
                 }).fail(function() {
                     alert("Gagal mengambil detail data.");
                 });
             });



             $(document).on("click", ".btn-edit", function() {
                 let id = $(this).data("id");
                 $.ajax({
                     url: "/latihan/" + id + "/edit",
                     method: "GET",
                     success: function(res) {
                         $("#edit_id").val(res.id);
                         $("#edit_nama").val(res.nama);
                         $("#edit_deskripsi").val(res.deskripsi);
                         $("#edit_link").val(res.link);

                         if (res.gambar) {
                             $("#preview_gambar")
                                 .attr("src", "/storage/" + res.gambar)
                                 .show();
                         } else {
                             $("#preview_gambar").hide();
                         }


                         $("#editModal").modal("show");
                     },
                 });
             });

             // update data
             $("#formEditLatihan").on("submit", function(e) {
                 e.preventDefault();

                 let id = $("#edit_id").val();
                 let formData = new FormData(this);

                 $.ajax({
                     url: "/latihan/" + id + "/update",
                     method: "POST",
                     data: formData,
                     processData: false,
                     contentType: false,
                     success: function(res) {
                         if (res.success) {
                             alert(res.message);
                             $("#editModal").modal("hide");
                             $("#formEditLatihan")[0].reset();
                             location.reload(); // refresh tabel / data
                         }
                     },
                     error: function(xhr) {
                         alert("Terjadi kesalahan saat update");
                     },
                 });
             });

             // Delete
             // Hapus data
             $(document).on("click", ".btn-delete", function() {
                 let id = $(this).data("id");

                 if (confirm("Yakin ingin menghapus data ini?")) {
                     $.ajax({
                         url: "/latihan/" + id,
                         type: "DELETE",
                         data: {
                             _token: "{{ csrf_token() }}"
                         },
                         success: function(res) {
                             if (res.success) {
                                 alert(res.message);
                                 $('#table-latihan').DataTable().ajax.reload(null,
                                     false);
                             } else {
                                 alert("Gagal menghapus data!");
                             }
                         },
                         error: function(xhr) {
                             console.error(xhr);
                             alert("Terjadi kesalahan saat menghapus!");
                         }
                     });
                 }
             });

         });
     </script>
 @endpush
