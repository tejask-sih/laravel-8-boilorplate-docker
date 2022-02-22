<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRefMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ref_media', function (Blueprint $table) {
            $table->id();          
            $table->string('storage',10)->comment = 'AS3 (Amezon Simple Storage Service) BK (Backup)';
            $table->string('url',400);
            $table->string('mime_type',50);
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
        Schema::dropIfExists('ref_media');
    }
}
