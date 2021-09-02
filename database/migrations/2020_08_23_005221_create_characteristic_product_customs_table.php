<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCharacteristicProductCustomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('characteristic_product_customs', function (Blueprint $table) {
            $table->id();
            $table->string('text')->nullable();
            $table->string('color')->nullable();
            $table->string('font')->nullable();
            $table->text('observations')->nullable();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('order_product_id');
            $table->unsignedBigInteger('type_characteristic_category_id')->nullable();
            $table->unsignedBigInteger('characteristic_category_id')->nullable();
            $table->unsignedBigInteger('image_side_id')->nullable();
            $table->timestamps();
            $table->foreign('image_side_id')
                ->references('id')
                ->on('image_sides');
            $table->foreign('product_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');
            $table->foreign('order_product_id')
                ->references('id')
                ->on('order_products')
                ->onDelete('cascade');
            $table->foreign('type_characteristic_category_id', 't_ch_cat_cus_id_foreign')
                ->references('id')
                ->on('type_characteristic_categories')
                ->onDelete('cascade');
            $table->foreign('characteristic_category_id', 'ch_cat_cus_id_foreign')
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
        Schema::dropIfExists('characteristic_product_customs');
    }
}
