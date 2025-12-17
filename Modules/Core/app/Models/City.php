<?php

namespace Modules\Core\app\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'name_ar',
        'name_en',
        'is_active',
    ];

    public function branches(): HasMany
    {
        return $this->hasMany(Branch::class);
    }
}
