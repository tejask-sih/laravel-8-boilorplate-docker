<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMstCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {       
        Schema::create('mst_company', function (Blueprint $table) {
            $table->id();           
            $table->integer('brand_id')->unsigned()->index();
            $table->string('name',50);
            $table->string('addess_line1',100);
            $table->string('addess_line2',100);
            $table->string('city',50);
            $table->string('state',20);
            $table->string('zipcode',10);
            $table->string('contact_person',50);
            $table->string('designation',20);
            $table->string('email',100);
            $table->string('primany_number',20);
            $table->string('alternate_number',20);
            $table->integer('email_header_id')->comment = 'AS3 file in /branding folder';
            $table->text('email_footer');
            $table->string('api_key',100);
            $table->integer('logo_id')->comment = 'AS3 file in /branding folder';
            $table->integer('hero_image_id')->comment = 'AS3 file in /branding folder';
            $table->integer('favicon_id');
            $table->string('theme_color',20);
            $table->string('as3_bucket',50);
            $table->tinyInteger('session_timeout');
            $table->double('cash_limit',10,2);
            $table->double('crtm',10,2)->default('1000');
            $table->double('handling_charges',10,2)->default('1500');
            $table->double('fastag',10,2)->default('600');
            $table->double('municipality_tax',10,2)->default('0');
            $table->double('tcs',10,2)->default('0');
            $table->double('pms',10,2)->default('0');
            $table->double('extended_warrenty',10,2)->default('0');
            $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
        });
        
        Schema::table('mst_company', function($table) {
           $table->foreign('brand_id')->references('id')->nullable()->on('mst_brands')->change();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::dropIfExists('mst_company');
        Schemachema::table('mst_company', function($table) {
           $table->foreign('brand_id')->references('id')->on('mst_brands');
        });
    }
}
