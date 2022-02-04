<?php
namespace App\Traits;
use DB;
use Auth;
use Validator;
use Carbon\Carbon;

trait CommonTrait {

    // public static $sitename  = 'Admin';
    // public static $siteemail = 'admin@gmail.com';
    /* 
        add all logs in table 
        created by Rahul 04/01/2022 
    */
    public function auditLog($actionName)
    {        
        $insertData = [            
            'user_type'   => 'App\Models\User',
            'auditable_id' => auth()->user()->id,
            'auditable_type' => "Users",
            'event'      => $actionName,
            'url'        => request()->fullUrl(),
            'ip_address' => request()->getClientIp(),
            'user_agent' => request()->userAgent(),
            'tags'       => $actionName,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'user_id'    => auth()->user()->id,
        ];       
        
        DB::table('cms_audit')->insert($insertData);  
    }

}