<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * COACH IA: ogni domanda fatta all'IA e la sua risposta, così le rileggi dopo.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coach_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('question');
            $table->longText('answer');
            $table->string('model', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coach_reports');
    }
};
