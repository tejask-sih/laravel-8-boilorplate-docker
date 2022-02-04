<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([  
            Brandseeder::class,          
            CompanySeeder::class, 
            Departmentseeder::class, 
            DesignationSeeder::class, 
            LevelSeeder::class, 
            LocationSeeder::class, 
            PayTypeSeeder::class, 
            PremisesSeeder::class, 
            PremisesTypeSeeder::class, 
            PrivilegeGroupSeeder::class,
            RoleSeeder::class,  
            EmailTemplateSeeder::class,  
            MediaSeeder::class,  
        ]);
    }
}
