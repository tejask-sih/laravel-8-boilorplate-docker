<?php

namespace App\Traits;
use DB;
use Auth;
use Validator;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Role;

trait HasPermissionsTrait {
    /* 
        check privilage
        created by Rahul 04/01/2022 
    */

    public function checkPrivilege($user_data,$privilege_id)
    {       
        if($user_data->overwrite === "1")
        {
            $privileges = $user_data->privileges;
            return strpos($privileges,$privilege_id) !== false ? 'Yes' : 'No';
        }else{           
            $str = array_filter(explode("#",$user_data->tcode));       
            $Role = Role::where('id',$str[1])->first(['privileges']);           
            $privileges = $Role->privileges;
            return strpos($privileges,$privilege_id) !== false ? 'Yes' : 'No';           
        } 
    }


}