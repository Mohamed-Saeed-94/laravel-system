<?php

namespace Modules\Core\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'city_id',
        'name_ar',
        'name_en',
        'address',
        'is_active',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'branch_departments')
            ->withPivot(['is_active'])
            ->withTimestamps();
    }

    public function jobTitles(): BelongsToMany
    {
        return $this->belongsToMany(JobTitle::class, 'branch_job_titles')
            ->withPivot(['is_active'])
            ->withTimestamps();
    }
}
