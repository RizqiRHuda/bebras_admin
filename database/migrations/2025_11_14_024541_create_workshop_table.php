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
        Schema::create('workshop', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_tahun');
            $table->foreign('id_tahun')->references('id')->on('tahun_workshop')->onDelete('cascade');
            $table->string('title')->nullable();
            $table->string('lokasi')->nullable();
            $table->date('tanggal')->nullable();
            $table->string('gambar')->nullable();
            $table->json('konten')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('workshop');
    }
};
