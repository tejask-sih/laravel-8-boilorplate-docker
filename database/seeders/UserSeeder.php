<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $superadmin = User::where('email','=','admin@gmail.com')->first();
        if($superadmin == null){
            $user = new User();
            $user->role_id = 1;
            $user->department_id = 1;
            $user->designation_id = 1;
            $user->level_id = 1;
            $user->location_id = 1;
            $user->premises_id = 1;
            $user->pay_type_id = 1;
            $user->reporting_to = 0;
            $user->avatar_id = 3;
            $user->username = 'admin@gmail.com';
            $user->password = bcrypt('silicon2021*');
            $user->name = 'Super';
            $user->address = 'Surat';
            $user->email = 'admin@gmail.com';
            $user->primary_number = '9876543210';
            $user->salary = '20000';
            $user->date_join = '2021-12-31';
            $user->tcode = '#1';
            $user->privileges = '#1#2#3#4#5#6#7#8#9#10#11#12#13#14#15#16#17#18#19#20#21#22#23#24#25#26#27#28#29#30#';
            $user->ip_address = '*';
            $user->save();
        }
    }
}
