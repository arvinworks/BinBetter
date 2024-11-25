<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClaimRewardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('claim_rewards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subs_expiry_id');
            $table->unsignedBigInteger('user_id');
            $table->date('date_claim');
            $table->integer('amount_claim');
            $table->timestamps();
           
            $table->foreign('subs_expiry_id')->references('id')->on('subscription_expirations')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('claim_rewards');
    }
}
