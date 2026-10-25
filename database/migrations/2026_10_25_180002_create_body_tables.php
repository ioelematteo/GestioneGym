<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * FORMA FISICA
 *
 * body_entries     una misurazione al giorno: peso, % grasso, vita (tutto facoltativo tranne il peso)
 * progress_photos  le foto dei progressi (il file sta sul disco configurato, qui c'è solo il percorso)
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('body_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('measured_on');
            $table->decimal('weight_kg', 5, 2);
            $table->decimal('body_fat', 4, 1)->nullable();
            $table->decimal('waist_cm', 5, 1)->nullable();
            $table->string('notes', 255)->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'measured_on']);
        });

        Schema::create('progress_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('taken_on');
            $table->string('pose', 10)->default('fronte'); // fronte | lato | retro
            $table->string('path');
            $table->string('notes', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progress_photos');
        Schema::dropIfExists('body_entries');
    }
};
