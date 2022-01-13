<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\Location;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Location::firstOrCreate(
            [
                'name' => 'Ahmedabd','zone'=>'A','primary_number'=>'9876543210'
            ]);
        Location::firstOrCreate(['name' => 'Vadodara','zone'=>'A','primary_number'=>'9638527410']);
        Location::firstOrCreate(['name' => 'Surat','zone'=>'A','primary_number'=>'9873216540']);
        Location::firstOrCreate(['name' => 'Mehsana','zone'=>'A','primary_number'=>'987456123']);
    }
}
