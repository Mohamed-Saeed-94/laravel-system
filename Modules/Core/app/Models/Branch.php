<?php

namespace Modules\Core\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Branch extends Model
{
    use CrudTrait;
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

}
