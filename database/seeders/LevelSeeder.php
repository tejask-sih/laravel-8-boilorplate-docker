<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Level;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $initiallevels = [
            "Level 0", 
            "Level 1",  
            "Level 2",
            "Level 3",
            "Level 4",
        ];
        
        foreach ($initiallevels as $level) {
            Level::firstOrCreate([
                'name' =>  $level,
            ]);     
        }
    }
}
