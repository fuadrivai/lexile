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
        Schema::create('answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id');
            $table->foreignId('passage_id');
            $table->integer('total_questions')->comment('Total number of questions in the passage');
            $table->integer('score')->default(0)->comment('Number of correct answers given by the student');
            $table->integer('estimated_lexile')->default(0)->comment('Estimated Lexile level based on student performance');
            $table->integer('total_time')->comment('Total time taken by the student to answer the passage in seconds');
            $table->integer('durations')->comment('Duration of the passage (in minutes)');
            $table->integer('total_answered')->default(0)->comment('Total number of questions answered by the student');
            $table->integer('correct_answers')->default(0)->comment('Number of correct answers given by the student');
            $table->string('performance')->nullable()->comment('Performance level of the student (e.g., Excellent, Good, Average, Poor)');
            $table->integer('lexile_level')->comment('Lexile level of the passage answered by the student');
            $table->integer('grade')->comment('Grade level of the passage answered by the student');
            $table->string('topic')->comment('Topic of the passage answered by the student');
            $table->boolean('is_passed')->default(true)->comment('Indicates whether the student passed the passage based on their score');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answers');
    }
};
