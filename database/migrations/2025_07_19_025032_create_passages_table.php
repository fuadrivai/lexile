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
        Schema::create('passages', function (Blueprint $table) {
            $table->id();
            $table->integer('grade')->comment('Grade level of the passage');
            $table->string('topic')->comment('Topic of the passage');
            $table->integer('lexile_level')->comment('Lexile level of the passage');
            $table->integer('duration')->comment('Duration of the passage (in minutes)');
            $table->text('passage')->comment('The passage text');
            $table->boolean('is_active')->default(true)->comment('Indicates whether the passage is active or not');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('passages');
    }
};
