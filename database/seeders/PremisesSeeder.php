<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\Premises;

class PremisesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Premises::firstOrCreate(['location_id' => 1,'type_id' => 1,'name'=>'Private @ Sanand Chokdi','addess_line1'=>'Ahemedabad','addess_line2'=>'Sanandchowkdi','city_id'=>1,'state_id'=>1,'zipcode'=>'1562356','primary_number' => '9869589659']);
    }
}
