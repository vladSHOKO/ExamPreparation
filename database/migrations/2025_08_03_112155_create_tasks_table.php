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
            $table->string('path');
            $table->timestamps();
        });

        Schema::create('tasks', function (Blueprint $table) {
            $table->id()->autoIncrement()->unique();
            $table->text('description')->isNotEmpty();
            $table->string('answer')->isNotEmpty();
            $table->string('subject')->isNotEmpty();
            $table->string('type')->isNotEmpty();
            $table->foreignId('additional_files')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('additional_files');
    }
};
