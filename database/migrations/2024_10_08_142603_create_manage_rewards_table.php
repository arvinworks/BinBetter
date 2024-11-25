<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateManageRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('manage_rewards', function (Blueprint $table) {
            $table->id();
            $table->string('reward_type')->unique();
            $table->integer('reward_amount');
            $table->integer('reward_expiration_value')->nullable();
            $table->string('reward_expiration_type',50)->nullable();
            $table->enum('status', ['Active', 'Inactive'])->nullable();
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
        Schema::dropIfExists('manage_rewards');
    }
}
