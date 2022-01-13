<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_locations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name',50);           
            $table->string('zone');
            $table->string('primary_number',20);
            $table->string('alternate_number1',20)->nullable();
            $table->string('alternate_number2',20)->nullable();
            $table->longText('address1')->nullable();
            $table->longText('address2')->nullable();
            $table->longText('address3')->nullable();
            $table->string('city')->nullable();
            $table->string('zipcode')->nullable();
            $table->string('state')->nullable();
            $table->string('sales_contact')->nullable();
            $table->string('sales_phone')->nullable();
            $table->string('sales_email')->nullable();
            $table->string('service_contact')->nullable();
            $table->string('service_phone')->nullable();
            $table->string('service_email')->nullable();
            $table->integer('dms_costing')->default('0');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });

        DB::statement("ALTER TABLE `mst_locations` ADD `status` TINYINT(1) DEFAULT 1 COMMENT '1= active, 0 = inactive' AFTER `name`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_locations');
    }
}
