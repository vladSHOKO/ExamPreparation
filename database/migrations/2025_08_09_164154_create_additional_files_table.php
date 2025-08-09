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
        Schema::create('additional_files', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('path')->unique()->isNotEmpty();
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade')->isNotEmpty();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('additional_files');
    }
};
