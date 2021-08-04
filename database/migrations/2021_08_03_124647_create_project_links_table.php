<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('project_links', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('text', 200);
            $table->string('url');
            $table->string('icon', 100)->nullable();
            $table->unsignedInteger('position');

            $table->foreignId('project_id')->constrained();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_links');
    }
}
