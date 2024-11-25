<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resident_id')->constrained('users')->onDelete('cascade');
            $table->string('type');
            $table->string('address')->nullable();
            $table->text('photo')->nullable();
            $table->string('video_url')->nullable();
            $table->text('description');
            $table->enum('status', ['Pending', 'Completed'])->default('Pending');
            $table->integer('report_status')->default(0);
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
        Schema::dropIfExists('post_reports');
    }
}
