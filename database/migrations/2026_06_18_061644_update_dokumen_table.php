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
        Schema::create('dokumen', function (Blueprint $table) {
            $table->id();

            // RELASI KE SURAT
            $table->foreignId('surat_id')
                ->constrained('surat')
                ->onDelete('cascade');

            $table->string('nama_file');
            $table->string('file_path');

            $table->enum('format', ['PDF', 'JPG', 'PNG']);
            $table->integer('ukuran');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
