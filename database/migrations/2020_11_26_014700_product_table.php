<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class ProductTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('t_featured_products', function (Blueprint $table) {
            $table->bigIncrements('product_no');
            $table->string('name', 50);
            $table->longText('description');
            $table->string('price', 50);
            $table->longText('image');
            $table->timestamp('ins_timestamp')->useCurrent();
            $table->timestamp('upd_timestamp')->default(DB::raw('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'))->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('t_featured_products');
    }
}
