<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PrivilegeGroup; 

class Privilege extends Model
{
    use HasFactory;
    protected $table = 'privileges';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function privilegeGroup(){
        return $this->belongsTo(PrivilegeGroup::class,'group_id');
    }
}
