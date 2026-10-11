<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();

            // RELASI KE USERS (1:1)
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // DATA AKADEMIK
            $table->string('nim')->unique();
            $table->string('prodi');
            $table->string('fakultas');
            $table->year('angkatan');
            $table->decimal('ipk', 3, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
