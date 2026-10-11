<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('beasiswa', function (Blueprint $table) {
            $table->id();

            // INFORMASI DASAR
            $table->string('judul');
            $table->enum('jenis', ['internal', 'eksternal']);
            $table->text('deskripsi');

            // KONFIGURASI BEASISWA
            $table->integer('kuota')->nullable();
            $table->date('deadline');

            // STATUS SYSTEM
            $table->enum('status', ['aktif', 'nonaktif'])->default('aktif');

            // RELASI ADMIN/STAF PEMBUAT
            $table->foreignId('created_by')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('beasiswa');
    }
};