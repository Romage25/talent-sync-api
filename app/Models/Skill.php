<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Skill extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'created_by_user',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_skills')->withTimestamps();
    }

    public function jobs(): BelongsToMany
    {
        return $this->belongsToMany(CompanyJob::class, 'job_skills', 'skill_id', 'job_id')->withTimestamps();
    }
}
