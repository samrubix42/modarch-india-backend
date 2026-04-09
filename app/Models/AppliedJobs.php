<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class AppliedJobs extends Model
{
    protected $fillable = [
        'job_profile_id',
        'job_title',
        'name',
        'email',
        'phone',
        'city',
        'portfolio_url',
        'resume_path',
        'portfolio_path',
        'message',
        'status',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'reviewed_at' => 'datetime',
        ];
    }

    public function jobProfile(): BelongsTo
    {
        return $this->belongsTo(JobProfile::class);
    }
}
