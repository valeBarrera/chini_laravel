<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharacteristicProductNativesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('characteristic_product_natives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('characteristic_category_id');
            $table->unsignedBigInteger('type_characteristic_category_id');
            $table->timestamps();
            $table->foreign('product_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');
            $table->foreign('characteristic_category_id', 'ch_cat_nat_id_foreign')
                ->references('id')
                ->on('characteristic_categories')
                ->onDelete('cascade');
            $table->foreign('type_characteristic_category_id', 't_ch_cat_nat_id_foreign')
                ->references('id')
                ->on('type_characteristic_categories')
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
        Schema::dropIfExists('characteristic_product_natives');
    }
}
