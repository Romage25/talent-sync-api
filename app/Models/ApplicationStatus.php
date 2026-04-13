<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApplicationStatus extends Model
{
    use SoftDeletes;

    protected $fillable = ['name'];

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'applicant_status_id');
    }
}
