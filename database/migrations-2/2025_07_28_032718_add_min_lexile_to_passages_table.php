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
        Schema::table('passages', function (Blueprint $table) {
            $table->integer('min_lexile')->default(200)->nullable()->after('lexile_level')
                ->comment('Minimum Lexile level of the passage');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('passages', function (Blueprint $table) {
            //
        });
    }
};
