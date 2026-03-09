<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Denormalize evaluation_answers by replacing the question_id FK
 * with point-in-time snapshot columns. This makes historical reports
 * immutable even after Questions are edited or deleted.
 *
 * Since SQLite does not support dropping columns or foreign keys directly,
 * we drop and recreate the entire table.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::drop('evaluation_answers');

        Schema::create('evaluation_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained()->cascadeOnDelete();

            // Point-in-time snapshots
            $table->text('question_text');
            $table->string('dimension_name');
            $table->string('measurement_name');
            $table->json('recommendations')->nullable();
            $table->json('activities')->nullable();
            $table->json('goals')->nullable();

            $table->tinyInteger('score');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::drop('evaluation_answers');

        Schema::create('evaluation_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('evaluation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->tinyInteger('score');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }
};
