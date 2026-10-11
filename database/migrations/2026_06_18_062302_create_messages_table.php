<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('chat_id')
                  ->constrained('live_chats')
                  ->onDelete('cascade');

            $table->foreignId('sender_id')
                  ->constrained('users')
                  ->onDelete('cascade');

            $table->text('message');

            $table->timestamp('sent_at')->useCurrent();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};