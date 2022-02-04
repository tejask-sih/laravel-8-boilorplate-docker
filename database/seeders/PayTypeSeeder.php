<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PayType;

class PayTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $initialPayType = [
            "Regular", 
            "Vouchar", 
        ];        
        foreach ($initialPayType as $type) {
            PayType::firstOrCreate([
                'name' =>  $type,
            ]);     
        }
    }
}
