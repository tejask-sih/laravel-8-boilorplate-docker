<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomAuditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Contracts\UserResolver;
use App\Models\Admin\States;
use App\Models\Admin\Cities;

class InquiryArea extends Model implements AuditableContract
{
    use HasFactory,CustomAuditable;   
    protected $table = "mst_areas";
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = ['id'];

    /**
    * Create relation with state
    */
    public function state(){
        return $this->hasOne(States::class,'id','state_id');
    }

    /**
    * Create relation with city
    */
    public function city(){
        return $this->hasOne(Cities::class,'id','city_id');
    }

    /**
    * Create relation with location
    */
    public function location(){
        return $this->hasOne(Location::class,'id','location_id');
    }
}
