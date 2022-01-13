<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\PremisesType;

class PremisesTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PremisesType::firstOrCreate(['name' => 'Private']);
        PremisesType::firstOrCreate(['name' => 'Commercial']);
    }
}
