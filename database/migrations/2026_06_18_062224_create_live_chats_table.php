<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('live_chats', function (Blueprint $table) {
            $table->id();

            // PARTICIPANTS
            $table->foreignId('mahasiswa_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->foreignId('staff_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            // STATUS CHAT
            $table->enum('status', ['aktif', 'selesai'])
                  ->default('aktif');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('live_chats');
    }
};