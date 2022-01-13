<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstPremisesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mst_premises', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('location_id')->unsigned();
            $table->integer('type_id')->unsigned();
            $table->string('name',50);
            $table->string('short_name',50);
            $table->string('addess_line1',100);
            $table->string('addess_line2',100);
            $table->integer('city_id');
            $table->integer('state_id');
            $table->string('zipcode',10);
            $table->string('primary_number',20);
            $table->string('alternate_number1',20)->nullable();
            $table->string('alternate_number2',20)->nullable();
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });

        Schema::table('mst_premises', function($table) {
           $table->foreign('location_id')->references('id')->on('mst_locations');
           $table->foreign('type_id')->references('id')->on('mst_premises_types');
        });

         DB::statement("ALTER TABLE `mst_premises` ADD `status` TINYINT(1) DEFAULT 1 COMMENT '1= active, 0 = inactive' AFTER `alternate_number2`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mst_premises');
    }
}
