<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;

class HasilPengumumanImport implements ToCollection
{
    protected $id_tahun;
    protected $id_kategori;

    public function __construct($id_tahun, $id_kategori)
    {
        $this->id_tahun = $id_tahun;
        $this->id_kategori = $id_kategori;
    }

    public function collection(Collection $rows)
    {
        $data = [];

        foreach ($rows->skip(1) as $row) {
            $data[] = [
                'id_tahun'      => $this->id_tahun,
                'id_kategori'   => $this->id_kategori,
                'nama'          => $row[0],
                'gender'        => $row[1],
                'kota'          => $row[2],
                'bebras_biro'   => $row[3],
                'username'      => $row[4],
                'skor'          => (float) $row[5],
                'rank_percent'  => (float) str_replace(',', '.', $row[6]),
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }

        if ($data) {
            DB::table('hasil_pengumuman')->insert($data);
        }
    }
}
