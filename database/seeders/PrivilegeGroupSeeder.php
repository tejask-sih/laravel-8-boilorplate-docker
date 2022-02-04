<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PrivilegeGroup;

class PrivilegeGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $add_to_seq_no = 5;
        $seq_no = 0;
        $dynamicPrivilegeGroups = [
            'Administration' => [
                'States' => '/administration/states', 
                'Cities' => '/administration/cities',
                'Areas' => '/administration/areas', 
                'Inquiry Areas' => '/administration/inquiry-areas',
                'Company Settings' => '',               
                'Locations' => '/administration/locations',
                'Premises Types' => '/administration/premises-types',
                'Premises' => '/administration/premises',
            ],           
            'Manage Inquiries'            
        ];
        $privilege_type = ['Create','Edit','Change Status','Delete'];

        $other_privilege_type = [
            'Company Settings' => ['Change Profile','System Preferences','Change Branding']
        ];

        $now = \Carbon\Carbon::now()->format('Y-m-d H:i:s');

        foreach ($dynamicPrivilegeGroups as $group => $privileges ) {
            $groupName = ucwords(is_string($group) ? $group : $privileges);

            $privilegeGroup = PrivilegeGroup::firstOrCreate(
                ['name' => $groupName ],
                ['created_at' => $now, 'updated_at' => $now]
            );

            if(is_array($privileges)){
                foreach ($privileges as $privilege => $controller) {
                    $seq_no = $seq_no + $add_to_seq_no;
                    $prilageName = ucwords($privilege);
                    $p = $privilegeGroup->privilege()->firstOrCreate(
                        ['name' => $prilageName ],
                        ['parent_id' => 0, 'controller' => $controller, 'created_at' => $now, 'updated_at' => $now, 'Tcode' => '', 'seqno' => $seq_no]
                    );
                    $p_id = $p->id;
                    $tcode = '#'.$p->id.'#';
                    $p->update(['Tcode' => $tcode]);

                    // check the privilege has other privilege type or we can use common
                    if(isset($other_privilege_type[$privilege])){
                        $types = $other_privilege_type[$privilege];
                    } else {
                        $types = $privilege_type;
                    }

                    foreach ($types as $type) {
                        $seq_no = $seq_no + $add_to_seq_no;
                        $prilageName = ucwords($type);
                        $p_t = $privilegeGroup->privilege()->firstOrCreate(
                            ['name' => $prilageName, 'parent_id' => $p_id ],
                            ['created_at' => $now, 'controller' => $controller, 'updated_at' => $now, 'Tcode' => '', 'seqno' => $seq_no]
                        );

                        $p_tcode = '#'.$p_t->id.$tcode;
                        $p_t->update(['Tcode' => $p_tcode]);
                    }

                }
                
            }
            
        }
    }
}
