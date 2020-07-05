<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('model');
            $table->string('image');
            $table->float('price');

            $table->unsignedBigInteger('brand_id');
            $table->foreign('brand_id')
                  ->references('id')
                  ->on("brand")
                  ->onDelete('cascade');

            $table->unsignedBigInteger('cat_id');
            $table->foreign('cat_id')
                  ->references('id')
                  ->on("category")
                  ->onDelete('cascade');
            $table->enum('type', ['LAITKLIMAT', 'VESNAKLIMAT'])->default("LAITKLIMAT");

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
        Schema::dropIfExists('Product');
    }
}
