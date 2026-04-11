<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Applicant extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'headline',
        'bio',
        'expected_salary_min',
        'expected_salary_max',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
