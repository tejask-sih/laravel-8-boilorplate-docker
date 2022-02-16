<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('role_id')->unsigned();
            $table->integer('avatar_id')->comment ='A file stored on S3 under /avatars folder';
            $table->string('username',20);
            $table->string('password',800);
            $table->string('name',100);
            $table->string('email',100);
            $table->string('primary_number',20);
            $table->integer('otp')->nullable();
            $table->text('remember_token')->nullable();
        });

        // Schema::table('users', function($table) {
        //    $table->foreign('role_id')->references('id')->on('mst_roles');
        //    $table->foreign('department_id')->references('id')->on('mst_departments');
        //    $table->foreign('designation_id')->references('id')->on('mst_designations');
        //    $table->foreign('level_id')->references('id')->on('mst_level');
        //    $table->foreign('location_id')->references('id')->on('mst_locations');
        //    $table->foreign('premises_id')->references('id')->on('mst_premises');
        //    $table->foreign('pay_type_id')->references('id')->on('pay_types');
        // });

        DB::statement("ALTER TABLE `users` ADD `is_default` TINYINT(1) DEFAULT 1 COMMENT '1= active, 0 = inactive' AFTER `remember_token`");

        DB::statement("ALTER TABLE `users` CHANGE `otp` `otp` INT(6) NULL DEFAULT NULL");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
