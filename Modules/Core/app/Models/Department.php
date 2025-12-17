<?php

namespace Modules\Core\App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'is_active',
    ];

    public function jobTitles(): HasMany
    {
        return $this->hasMany(JobTitle::class);
    }

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class, 'branch_departments')
            ->withPivot(['is_active'])
            ->withTimestamps();
    }
}
