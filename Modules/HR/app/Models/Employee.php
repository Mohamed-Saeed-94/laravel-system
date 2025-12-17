<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'hire_date',
        'branch_id',
        'department_id',
        'job_title_id',
        'is_active',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\Branch::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\Department::class);
    }

    public function jobTitle(): BelongsTo
    {
        return $this->belongsTo(\Modules\Core\Models\JobTitle::class);
    }
}
