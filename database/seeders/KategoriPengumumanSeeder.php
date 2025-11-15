<?php
namespace Database\Seeders;

use App\Models\Pengumuman\KategoriPengumuman;
use Illuminate\Database\Seeder;

class KategoriPengumumanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $kategori = [
            [
                'nama_kategori' => 'Si Kecil',
                'deskripsi'     => 'Si Kecil (hingga Kelas 3 SD dan MI)',
            ],
            [
                'nama_kategori' => 'Siaga',
                'deskripsi'     => 'Siaga (Kelas 4–6 SD dan MI)',
            ],
            [
                'nama_kategori' => 'Penggalang',
                'deskripsi'     => 'Penggalang (SMP dan MTs)',
            ],
            [
                'nama_kategori' => 'Penegak',
                'deskripsi'     => 'Penegak (SMA, MA, dan SMK)',
            ],
        ];

        KategoriPengumuman::insert($kategori);
    }

}
