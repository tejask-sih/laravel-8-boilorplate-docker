<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Designation;

class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $initialdesignation= [
            "Receptionist", 
            "Sales Executive", 
            "Team Leader",
            "Sales Manager",
        ];
        
        foreach ($initialdesignation as $designation) {
            Designation::firstOrCreate([
                'name' =>  $designation,'department_id' => 1
            ]);     
        }
    }
}
