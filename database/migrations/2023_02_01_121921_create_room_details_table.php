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
            $table->boolean('available')->default(true);
            $table->integer('price');
            $table->text('desc');
            $table->string('image');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('address');
            $table->json('amenities');
            $table->boolean('isShared')->default(false);
            $table->json('conditions')->nullable();
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
