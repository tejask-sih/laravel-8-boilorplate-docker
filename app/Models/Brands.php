<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\CustomAuditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;
use OwenIt\Auditing\Contracts\UserResolver;

class Brands extends Model implements AuditableContract
{
    use HasFactory,CustomAuditable;
    protected $table = "mst_brands";
    protected $guarded = ['id'];

}
