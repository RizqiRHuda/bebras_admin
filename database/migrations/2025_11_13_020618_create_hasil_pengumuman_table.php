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
        Schema::create('hasil_pengumuman', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tahun')->constrained('tahun_pengumuman')->onDelete('cascade');
            $table->foreignId('id_kategori')->constrained('kategori_pengumuman')->onDelete('cascade');
            $table->string('nama');
            $table->enum('gender', ['Laki-Laki', 'Perempuan']);
            $table->string('kota');
            $table->string('bebras_biro');
            $table->string('username')->unique();
            $table->decimal('skor', 5, 2);      
            $table->decimal('rank_percent', 5, 2); 
            $table->index(['id_tahun', 'id_kategori']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_pengumuman');
    }
};
