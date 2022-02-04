<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomAuditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Contracts\UserResolver;
use App\Models\Admin\States;

class Cities extends Model implements AuditableContract
{
    use CustomAuditable;
    protected $table = "mst_cities";

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = ['id'];

    public $timestamps = false;

    public function states(){
        return $this->belongsTo(States::class,'state_id');
    }
}
