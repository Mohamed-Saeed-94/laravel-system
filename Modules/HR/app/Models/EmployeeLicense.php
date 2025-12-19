<?php

namespace Modules\HR\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class EmployeeLicense extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'type',
        'number',
        'issue_date',
        'expiry_date',
        'is_primary',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'is_primary' => 'boolean',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function files(): MorphMany
    {
        return $this->morphMany(EmployeeFile::class, 'fileable');
    }
}
