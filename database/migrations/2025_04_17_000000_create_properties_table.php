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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('title');
            $table->text('description');
            $table->string('status');
            $table->string('type');
            $table->decimal('price', 12, 2);
            $table->integer('bedrooms');
            $table->integer('bathrooms');
            $table->decimal('area', 10, 2);
            $table->string('district');
            $table->string('location');
            $table->string('address');
            $table->year('year_built');
            $table->string('video_tour')->nullable();
            $table->json('neighborhood')->nullable();
            $table->boolean('is_featured')->default(false);
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
        Schema::dropIfExists('properties');
    }
};
