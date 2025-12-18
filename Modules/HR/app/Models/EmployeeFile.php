<?php

namespace Modules\HR\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use finfo;

class EmployeeFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'fileable_id',
        'fileable_type',
        'category',
        'file_path',
        'file_name',
        'mime_type',
        'file_size',
        'side',
        'is_primary',
        'notes',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (EmployeeFile $file) {
            if ($file->category === 'employee_photo' && $file->is_primary) {
                $exists = EmployeeFile::query()
                    ->where('fileable_type', $file->fileable_type)
                    ->where('fileable_id', $file->fileable_id)
                    ->where('category', 'employee_photo')
                    ->where('is_primary', true)
                    ->when($file->id, fn ($query) => $query->where('id', '<>', $file->id))
                    ->exists();

                if ($exists) {
                    throw ValidationException::withMessages([
                        'is_primary' => __('Only one primary employee photo is allowed for each employee.'),
                    ]);
                }
            }

            if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
                $fullPath = Storage::disk('public')->path($file->file_path);
                $file->mime_type = $file->mime_type ?? mime_content_type($fullPath);
                $file->file_size = $file->file_size ?? Storage::disk('public')->size($file->file_path);
                $file->file_name = $file->file_name ?? basename($file->file_path);
            }
        });
    }

    public function fileable(): MorphTo
    {
        return $this->morphTo();
    }
}
