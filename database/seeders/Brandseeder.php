<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Brands;

class Brandseeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $initialBrands = [
            "CMS", 
            "DHL", 
            "Wetake express", 
            "DTDC",
            "Aramex Courier",
            "Blue Dart Courier",
            "Ecom Express",
            "FedEx Courier",
        ];

        foreach ($initialBrands as $brands) {
                Brands::firstOrCreate([
                    'name' =>  $brands,
                ]);     
        }
    }
}
