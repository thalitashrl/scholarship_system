<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surat', function (Blueprint $table) {
            $table->id();

            // RELASI MAHASISWA
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // JENIS SURAT
            $table->string('jenis_surat');
            $table->text('keperluan');

            // STATUS WORKFLOW
            $table->enum('status', [
                'pending',
                'diproses',
                'selesai',
                'ditolak'
            ])->default('pending');

            // TIMESTAMP PROSES
            $table->timestamp('tanggal_pengajuan')->useCurrent();
            $table->timestamp('tanggal_selesai')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surat');
    }
};