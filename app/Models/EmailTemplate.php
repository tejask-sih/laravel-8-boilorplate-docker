<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;
    protected $table = "ref_Email";

    protected $fillable = ['id', 'purpose', 'subject' ,'body', 'status', 'created', 'modified'];

    protected $guarded = [];

    protected $appends = [
    ];
}
