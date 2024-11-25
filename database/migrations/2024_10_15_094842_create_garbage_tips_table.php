<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGarbageTipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('garbage_tips', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('photos')->nullable();
            $table->string('video')->nullable();
            $table->text('description');
            $table->integer('report_count')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('garbage_tips');
    }
}
