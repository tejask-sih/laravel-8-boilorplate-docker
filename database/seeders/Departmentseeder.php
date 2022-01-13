<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin\Departments;


class Departmentseeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Departments::firstOrCreate(['name' => 'Booking & Sales']);
    }
}
