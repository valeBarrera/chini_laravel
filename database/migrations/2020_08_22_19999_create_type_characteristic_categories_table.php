<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTypeCharacteristicCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('type_characteristic_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->tinyInteger('has_color');
            $table->tinyInteger('has_img');
            $table->tinyInteger('state');
            $table->integer('extra_price');
            $table->string('color')->nullable();
            $table->string('img')->nullable();
            $table->integer('design_leaf')->nullable();
            $table->unsignedBigInteger('characteristic_categories_id');
            $table->timestamps();
            $table->foreign('characteristic_categories_id', 't_ch_cat_id_foreign')
            ->references('id')
                ->on('characteristic_categories')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('type_characteristic_categories');
    }
}
