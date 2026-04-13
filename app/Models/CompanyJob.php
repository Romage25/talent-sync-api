<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyJob extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'created_by',
        'company_id',
        'job_status_id',
        'job_type_id',
        'job_setup_id',
        'title',
        'description',
        'location',
        'salary_min',
        'salary_max',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(JobType::class);
    }

    public function setup(): BelongsTo
    {
        return $this->belongsTo(JobSetup::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(JobStatus::class, 'job_status_id');
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'job_skills', 'job_id', 'skill_id')->withTimestamps();
    }
}
