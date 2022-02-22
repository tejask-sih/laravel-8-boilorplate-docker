<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrivilegesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('privileges', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('group_id')->unsigned();
            $table->integer('parent_id');
            $table->string('name',50);
            $table->string('controller',400);
            $table->string('Tcode',400);
            $table->integer('seqno');
            $table->timestamps();
        });

        Schema::table('privileges', function($table) {
            $table->foreign('group_id')->references('id')->on('privilege_groups');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('privileges');
    }
}
