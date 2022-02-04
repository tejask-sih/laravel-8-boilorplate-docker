<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = [
            'Admin' => '',
            'Sales Executive' => '',
            'Team Leader - Sales' => '',
            'Sales Manager' => '',
            'VP' => '',
            'MD' => '',
            'CEO' => '',
            'Operation Executive' => ''
        ];

        $now = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        foreach ($roles as $key => $value) {
            Role::firstOrCreate(
                ['name' => $key ],
                ['privileges' => $value, 'status' => 1, 'created_at' => $now, 'updated_at' => $now]
            );
        }
    }
}
