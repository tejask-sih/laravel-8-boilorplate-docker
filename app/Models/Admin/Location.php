<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomAuditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Contracts\UserResolver;

class Location extends Model implements AuditableContract
{
    use HasFactory,CustomAuditable;
    protected $table = "mst_locations";
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = ['id'];
}
