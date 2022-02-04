<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstBrandsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_brands', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',50)->comment = 'CMS,DHL, Wetake express, DTDC ';
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
        
        DB::statement("ALTER TABLE `mst_brands` ADD `status` TINYINT(1) DEFAULT 1 COMMENT '1= active, 0 = inactive' AFTER `name`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_brands');
    }
}
