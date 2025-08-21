<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurriculumAndSubjectToPassagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('passages', function (Blueprint $table) {
            $table->string('curriculum')->nullable()->after('min_lexile')->comment('curriculum of the passage');
            $table->string('subject')->nullable()->after('curriculum')->comment('subject of the passage');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('passages', function (Blueprint $table) {
            //
        });
    }
}
