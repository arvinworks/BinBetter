<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('resident_id');
            $table->unsignedBigInteger('post_report_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->text('comment');
            $table->timestamps();

            $table->foreign('resident_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('post_report_id')->references('id')->on('post_reports')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_comments');
    }
}
