<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Pengumuman\TahunPengumuman;
use App\Models\Pengumuman\KategoriPengumuman;
use App\Models\Pengumuman\HasilPengumuman;

class HasilPengumumanEmbedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Seed data untuk testing API Pengumuman dengan berbagai platform
     */
    public function run(): void
    {
        // Pastikan ada tahun
        $tahun2024 = TahunPengumuman::firstOrCreate(['tahun' => '2024']);
        $tahun2023 = TahunPengumuman::firstOrCreate(['tahun' => '2023']);
        
        // Pastikan ada kategori
        $siaga = KategoriPengumuman::firstOrCreate([
            'nama_kategori' => 'Siaga',
            'deskripsi' => 'Kelas 3-4 SD'
        ]);
        
        $penggalang = KategoriPengumuman::firstOrCreate([
            'nama_kategori' => 'Penggalang',
            'deskripsi' => 'Kelas 5-6 SD'
        ]);
        
        $penegak = KategoriPengumuman::firstOrCreate([
            'nama_kategori' => 'Penegak',
            'deskripsi' => 'Kelas 7-8 SMP'
        ]);
        
        $penegakLanjutan = KategoriPengumuman::firstOrCreate([
            'nama_kategori' => 'Penegak Lanjutan',
            'deskripsi' => 'Kelas 9-10 SMA'
        ]);

        // Contoh data dengan Google Sheets (bisa di-embed)
        HasilPengumuman::create([
            'id_tahun' => $tahun2024->id,
            'id_kategori' => $siaga->id,
            'description' => 'Hasil Pengumuman Kategori Siaga 2024',
            'platform' => 'google_sheets',
            'embed_url' => 'https://docs.google.com/spreadsheets/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit?usp=sharing',
            'is_uploaded' => false,
            'file_path' => null,
            
            // Field existing (isi dengan data dummy atau null jika tidak digunakan untuk embed)
            'nama' => 'N/A',
            'gender' => 'Laki-Laki',
            'kota' => 'N/A',
            'bebras_biro' => 'N/A',
            'username' => 'siaga_2024_embed',
            'skor' => 0.00,
            'rank_percent' => 0.00,
        ]);

        // Contoh data dengan OneDrive (tidak bisa di-embed, redirect)
        HasilPengumuman::create([
            'id_tahun' => $tahun2024->id,
            'id_kategori' => $penggalang->id,
            'description' => 'Hasil Pengumuman Kategori Penggalang 2024',
            'platform' => 'onedrive',
            'embed_url' => 'https://1drv.ms/x/s!AqZ1234567890',
            'is_uploaded' => false,
            'file_path' => null,
            
            'nama' => 'N/A',
            'gender' => 'Perempuan',
            'kota' => 'N/A',
            'bebras_biro' => 'N/A',
            'username' => 'penggalang_2024_embed',
            'skor' => 0.00,
            'rank_percent' => 0.00,
        ]);

        // Contoh data dengan File Upload PDF
        // Note: Pastikan file ada di storage/app/public/pengumuman/2024/penegak.pdf
        HasilPengumuman::create([
            'id_tahun' => $tahun2024->id,
            'id_kategori' => $penegak->id,
            'description' => 'Hasil Pengumuman Kategori Penegak 2024',
            'platform' => null,
            'embed_url' => null,
            'is_uploaded' => true,
            'file_path' => 'pengumuman/2024/penegak.pdf',
            
            'nama' => 'N/A',
            'gender' => 'Laki-Laki',
            'kota' => 'N/A',
            'bebras_biro' => 'N/A',
            'username' => 'penegak_2024_upload',
            'skor' => 0.00,
            'rank_percent' => 0.00,
        ]);

        // Contoh tahun 2023
        HasilPengumuman::create([
            'id_tahun' => $tahun2023->id,
            'id_kategori' => $siaga->id,
            'description' => 'Hasil Pengumuman Kategori Siaga 2023',
            'platform' => 'google_sheets',
            'embed_url' => 'https://docs.google.com/spreadsheets/d/1BxiMVs0XRA5nFMdKvBdBZjgmUUqptlbs74OgvE2upms/edit?usp=sharing',
            'is_uploaded' => false,
            'file_path' => null,
            
            'nama' => 'N/A',
            'gender' => 'Laki-Laki',
            'kota' => 'N/A',
            'bebras_biro' => 'N/A',
            'username' => 'siaga_2023_embed',
            'skor' => 0.00,
            'rank_percent' => 0.00,
        ]);

        $this->command->info('✅ Data pengumuman embed berhasil di-seed!');
        $this->command->info('📊 Total: 4 hasil pengumuman dengan berbagai platform');
        $this->command->warn('⚠️  Untuk file upload, pastikan file ada di: storage/app/public/pengumuman/2024/penegak.pdf');
    }
}
