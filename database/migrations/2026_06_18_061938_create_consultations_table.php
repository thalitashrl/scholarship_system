<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();

            // RELASI USER (MAHASISWA)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // TUJUAN KONSULTASI
            $table->enum('tujuan', ['staff', 'wd3']);

            // JENIS KONSULTASI
            $table->enum('jenis', ['tatap_muka', 'online']);

            // SCHEDULE
            $table->dateTime('jadwal');

            // STATUS WORKFLOW
            $table->enum('status', [
                'menunggu',
                'disetujui',
                'ditolak',
                'dialihkan',
                'selesai'
            ])->default('menunggu');

            // RELASI PENERIMA (STAFF / WD3)
            $table->foreignId('penerima_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->text('keterangan')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};