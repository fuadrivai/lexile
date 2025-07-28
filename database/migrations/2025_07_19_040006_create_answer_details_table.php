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
        Schema::create('answer_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('answer_id');
            $table->text('question_text')->comment('The text of the question being answered');
            $table->foreignId('question_id');
            $table->string('selected_option')->comment('The option selected by the student (A, B, C, or D)');
            $table->string('selected_option_text')->comment('The text of the option selected by the student (A, B, C, or D)');
            $table->string('correct_option')->comment('The correct option (A, B, C, or D)');
            $table->string('correct_option_text')->comment('The text of the correct option (A, B, C, or D)');
            $table->boolean('is_correct')->default(false)->comment('Whether the selected option is correct');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('answer_details');
    }
};
