<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pengumuman\TahunPengumuman;
use App\Models\Pengumuman\KategoriPengumuman;
use App\Models\Pengumuman\HasilPengumuman;

class HasilPengumumanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Seeder untuk testing API hasil pengumuman
     * dengan berbagai platform (Google Sheets, OneDrive, Upload)
     */
    public function run(): void
    {
        // Create tahun
        $tahun2024 = TahunPengumuman::firstOrCreate(['tahun' => '2024']);
        $tahun2023 = TahunPengumuman::firstOrCreate(['tahun' => '2023']);
        $tahun2022 = TahunPengumuman::firstOrCreate(['tahun' => '2022']);

        // Create kategori
        $kategoriSiaga = KategoriPengumuman::firstOrCreate([
            'nama_kategori' => 'Siaga',
            'deskripsi' => 'Kategori Siaga (SD Kelas 3-4)'
        ]);

        $kategoriPenggalang = KategoriPengumuman::firstOrCreate([
            'nama_kategori' => 'Penggalang',
            'deskripsi' => 'Kategori Penggalang (SD Kelas 5-6)'
        ]);

        $kategoriPenegak = KategoriPengumuman::firstOrCreate([
            'nama_kategori' => 'Penegak',
            'deskripsi' => 'Kategori Penegak (SMP Kelas 7-8)'
        ]);

        $kategoriPandega = KategoriPengumuman::firstOrCreate([
            'nama_kategori' => 'Pandega',
            'deskripsi' => 'Kategori Pandega (SMP Kelas 9 - SMA Kelas 10)'
        ]);

        // Hasil pengumuman 2024
        
        // 1. Google Sheets example (can embed)
        HasilPengumuman::create([
            'id_tahun' => $tahun2024->id,
            'id_kategori' => $kategoriSiaga->id,
            'description' => 'Hasil Pengumuman Bebras Challenge 2024 - Kategori Siaga',
            'platform' => 'google_sheets',
            'embed_url' => 'https://docs.google.com/spreadsheets/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit?usp=sharing',
            'is_uploaded' => false,
            'file_path' => null,
        ]);

        // 2. OneDrive example (cannot embed, redirect only)
        HasilPengumuman::create([
            'id_tahun' => $tahun2024->id,
            'id_kategori' => $kategoriPenggalang->id,
            'description' => 'Hasil Pengumuman Bebras Challenge 2024 - Kategori Penggalang',
            'platform' => 'onedrive',
            'embed_url' => 'https://onedrive.live.com/view.aspx?resid=example',
            'is_uploaded' => false,
            'file_path' => null,
        ]);

        // 3. Excel Online example (cannot embed, redirect only)
        HasilPengumuman::create([
            'id_tahun' => $tahun2024->id,
            'id_kategori' => $kategoriPenegak->id,
            'description' => 'Hasil Pengumuman Bebras Challenge 2024 - Kategori Penegak',
            'platform' => 'excel_online',
            'embed_url' => 'https://excel.office.com/view.aspx?example',
            'is_uploaded' => false,
            'file_path' => null,
        ]);

        // 4. Uploaded file example (can embed + download)
        // Note: Untuk testing, file_path harus ada file-nya di storage/app/public/pengumuman/
        HasilPengumuman::create([
            'id_tahun' => $tahun2024->id,
            'id_kategori' => $kategoriPandega->id,
            'description' => 'Hasil Pengumuman Bebras Challenge 2024 - Kategori Pandega',
            'platform' => 'upload',
            'embed_url' => null,
            'is_uploaded' => true,
            'file_path' => 'pengumuman/hasil_2024_pandega.xlsx', // Path relatif ke storage/app/public
        ]);

        // Hasil pengumuman 2023
        
        HasilPengumuman::create([
            'id_tahun' => $tahun2023->id,
            'id_kategori' => $kategoriSiaga->id,
            'description' => 'Hasil Pengumuman Bebras Challenge 2023 - Kategori Siaga',
            'platform' => 'google_sheets',
            'embed_url' => 'https://docs.google.com/spreadsheets/d/example2023/edit?usp=sharing',
            'is_uploaded' => false,
            'file_path' => null,
        ]);

        HasilPengumuman::create([
            'id_tahun' => $tahun2023->id,
            'id_kategori' => $kategoriPenggalang->id,
            'description' => 'Hasil Pengumuman Bebras Challenge 2023 - Kategori Penggalang',
            'platform' => 'upload',
            'embed_url' => null,
            'is_uploaded' => true,
            'file_path' => 'pengumuman/hasil_2023_penggalang.xlsx',
        ]);

        // Hasil pengumuman 2022
        
        HasilPengumuman::create([
            'id_tahun' => $tahun2022->id,
            'id_kategori' => $kategoriSiaga->id,
            'description' => 'Hasil Pengumuman Bebras Challenge 2022 - Kategori Siaga',
            'platform' => 'google_sheets',
            'embed_url' => 'https://docs.google.com/spreadsheets/d/example2022/edit?usp=sharing',
            'is_uploaded' => false,
            'file_path' => null,
        ]);

        $this->command->info('✅ Hasil Pengumuman seeder berhasil dijalankan!');
        $this->command->info('📊 Data untuk tahun: 2024, 2023, 2022');
        $this->command->info('📁 Platform: Google Sheets, OneDrive, Excel Online, Upload File');
    }
}
