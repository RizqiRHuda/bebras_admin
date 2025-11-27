<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('hasil_pengumuman', function (Blueprint $table) {
            // Drop kolom yang tidak diperlukan untuk embed approach
            $table->dropColumn([
                'nama',
                'gender',
                'kota',
                'bebras_biro',
                'username',
                'skor',
                'rank_percent'
            ]);
            
            // Tambah kolom untuk embed
            $table->text('embed_url')->nullable()->after('id_kategori');
            $table->string('file_path')->nullable()->after('embed_url');
            $table->boolean('is_uploaded')->default(false)->after('file_path');
            $table->string('platform')->nullable()->default('google_sheets')->after('is_uploaded');
            $table->text('description')->nullable()->after('platform');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hasil_pengumuman', function (Blueprint $table) {
            // Kembalikan kolom lama
            $table->string('nama')->after('id_kategori');
            $table->enum('gender', ['Laki-Laki', 'Perempuan'])->after('nama');
            $table->string('kota')->after('gender');
            $table->string('bebras_biro')->after('kota');
            $table->string('username')->unique()->after('bebras_biro');
            $table->decimal('skor', 5, 2)->after('username');
            $table->decimal('rank_percent', 5, 2)->after('skor');
            
            // Drop kolom embed
            $table->dropColumn(['embed_url', 'platform', 'description']);
        });
    }
};
