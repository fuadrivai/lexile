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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('passage_id');
            $table->text('question')->comment('The question text');
            $table->string('option_a')->comment('Answer option A');
            $table->string('option_b')->comment('Answer option B');
            $table->string('option_c')->comment('Answer option C');
            $table->string('option_d')->comment('Answer option D');
            $table->string('correct_answer')->comment('The correct answer');
            $table->boolean('is_active')->default(true)->comment('Indicates whether the question is active or not');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
