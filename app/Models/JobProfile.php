<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobProfile extends Model
{
    protected $fillable = [
        'job_title',
        'job_description',
        'is_active',
        'sort_order',
    ];
}
