<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->text('content')->isNotEmpty();
            $table->enum('role', ['user', 'assistant', 'system'])->isNotEmpty();
            $table->foreignId('task_session_id')->constrained('task_sessions')->cascadeOnDelete()->isNotEmpty();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_message');
    }
};
