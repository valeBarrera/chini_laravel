<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharacteristicCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('characteristic_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->tinyInteger('is_custom');
            $table->tinyInteger('is_optional');
            $table->tinyInteger('is_image');
            $table->tinyInteger('is_text');
            $table->tinyInteger('is_design');
            $table->tinyInteger('state');
            $table->integer('price')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('image_type_id')->nullable();
            $table->timestamps();
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');
            $table->foreign('image_type_id')
                ->references('id')
                ->on('image_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('characteristic_categories');
    }
}
