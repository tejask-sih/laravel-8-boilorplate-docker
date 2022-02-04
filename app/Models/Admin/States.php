<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomAuditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Contracts\UserResolver;
use App\Models\Admin\Cities;

class States extends Model implements AuditableContract
{
    use CustomAuditable;
    protected $table = "mst_states";

    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = ['id'];

    public $timestamps = false;

    /**
     * Create relation with city
    */
    public function cities(){
        return $this->hasMany(Cities::class,'state_id','id');
    }
}
