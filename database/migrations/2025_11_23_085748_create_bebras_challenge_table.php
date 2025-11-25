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
        Schema::create('bebras_challenge', function (Blueprint $table) {
            $table->id();
            $table->year('tahun');
            $table->string('title');
            $table->string('slug');
            $table->string('tagline')->nullable();
            $table->string('gambar')->nullable();
            $table->json('table_json')->nullable();
            $table->longText('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bebras_challenge');
    }
};
