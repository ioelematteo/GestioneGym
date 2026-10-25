<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * ALLENAMENTI
 *
 * exercises          catalogo esercizi (condiviso)          "Panca inclinata"
 * workouts           la scheda di un utente                  "Allenamento A"
 * workout_exercises  cosa c'è nella scheda + peso previsto   Panca inclinata, 4 serie, 8-10 rep, 16 kg
 * set_states         stati di una serie                      "Ultime kippate", "Spottato"...
 * training_sessions  un allenamento fatto (singolo workout)  23/10, Allenamento A, 64 min
 * session_sets       ogni serie fatta, con il SUO peso       Panca inclinata, serie 2, 16 kg × 9
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100)->unique();
            $table->timestamps();
        });

        Schema::create('workouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('color', 20)->default('rosso');
            $table->string('notes', 255)->nullable();
            $table->timestamps();
            $table->softDeletes(); // eliminato = nascosto, lo storico resta leggibile
        });

        Schema::create('workout_exercises', function (Blueprint $table) {
            $table->id();
            $table->foreignId('workout_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exercise_id')->constrained()->restrictOnDelete();
            $table->unsignedSmallInteger('position')->default(1);
            $table->unsignedTinyInteger('sets')->default(3);
            $table->string('reps_target', 20)->nullable();
            $table->decimal('weight_target', 6, 2)->nullable();
            $table->unsignedSmallInteger('rest_seconds')->default(90);
            $table->string('notes', 255)->nullable();
        });

        Schema::create('set_states', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50);
            $table->string('description', 150)->nullable();
            $table->string('color', 20)->default('grigio');
            $table->unsignedSmallInteger('position')->default(0);
        });

        Schema::create('training_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('workout_id')->constrained()->cascadeOnDelete();
            $table->dateTime('performed_at');
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'performed_at']);
            $table->index(['workout_id', 'performed_at']);
        });

        Schema::create('session_sets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('exercise_id')->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('set_number');
            $table->decimal('weight', 6, 2)->default(0);
            $table->unsignedSmallInteger('reps');
            $table->foreignId('set_state_id')->nullable()->constrained()->nullOnDelete();

            $table->index('exercise_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_sets');
        Schema::dropIfExists('training_sessions');
        Schema::dropIfExists('set_states');
        Schema::dropIfExists('workout_exercises');
        Schema::dropIfExists('workouts');
        Schema::dropIfExists('exercises');
    }
};
