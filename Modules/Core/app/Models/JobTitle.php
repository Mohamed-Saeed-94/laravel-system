<?php

namespace Modules\Core\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobTitle extends Model
{
    use CrudTrait;
    use HasFactory;

    protected $fillable = [
        'department_id',
        'name_ar',
        'name_en',
        'is_active',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

}
