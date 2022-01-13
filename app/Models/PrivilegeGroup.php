<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Privilege; 

class PrivilegeGroup extends Model
{
    protected $table = 'privilege_groups';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function privilege(){
        return $this->hasMany(Privilege::class,'group_id');
    }
}
