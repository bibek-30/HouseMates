<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_details', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique();
            $table->foreignId('user_id')->constrained();
            $table->string('state');
            $table->string('city');
            $table->string('zip');
            $table->boolean('available')->default(true);
            $table->integer('price');
            $table->string('desc');
            $table->string('image');
            $table->timestamps();
        });
        // $table->boolean('available')->default(true);

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('room_details');
    }
};
