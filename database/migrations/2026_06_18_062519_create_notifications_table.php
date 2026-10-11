<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            // PENERIMA NOTIFIKASI
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            // JENIS NOTIFIKASI
            $table->string('title');
            $table->text('message');

            // TIPE NOTIFIKASI
            $table->enum('type', [
                'surat',
                'beasiswa',
                'chat',
                'konsultasi',
                'system'
            ]);

            // STATUS BACA
            $table->enum('status', ['unread', 'read'])
                  ->default('unread');

            // OPTIONAL LINK (REDIRECT)
            $table->string('link')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};