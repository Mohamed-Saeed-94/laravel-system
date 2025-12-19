<?php

namespace Modules\HR\Models;

use Backpack\CRUD\app\Models\Traits\CrudTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Modules\Core\Models\Branch;
use Modules\Core\Models\Department;
use Modules\Core\Models\JobTitle;
use Modules\HR\Rules\DepartmentInBranch;
use Modules\HR\Rules\JobTitleInBranch;
use Modules\HR\Rules\JobTitleMatchesDepartment;

class Employee extends Model
{
    use CrudTrait;
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'employee_code',
        'full_name',
        'email',
        'gender',
        'branch_id',
        'department_id',
        'job_title_id',
        'hire_date',
        'termination_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'hire_date' => 'date',
        'termination_date' => 'date',
    ];

    protected static function booted(): void
    {
        static::saving(function (Employee $employee) {
            Validator::make($employee->getAttributes(), [
                'employee_code' => [
                    'required',
                    'string',
                    'max:30',
                    Rule::unique('employees', 'employee_code')->ignore($employee->id),
                ],
                'full_name' => ['required', 'string', 'max:200'],
                'email' => ['nullable', 'email', 'max:255'],
                'gender' => ['nullable', Rule::in(['male', 'female'])],
                'branch_id' => ['required', 'integer', 'exists:branches,id'],
                'department_id' => [
                    'required',
                    'integer',
                    'exists:departments,id',
                    new DepartmentInBranch($employee->branch_id),
                ],
                'job_title_id' => [
                    'required',
                    'integer',
                    'exists:job_titles,id',
                    new JobTitleInBranch($employee->branch_id),
                    new JobTitleMatchesDepartment($employee->department_id),
                ],
                'status' => ['required', Rule::in(['active', 'suspended', 'terminated'])],
            ])->validate();
        });
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function jobTitle(): BelongsTo
    {
        return $this->belongsTo(JobTitle::class);
    }

    public function phones(): HasMany
    {
        return $this->hasMany(EmployeePhone::class);
    }

    public function identities(): HasMany
    {
        return $this->hasMany(EmployeeIdentity::class);
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(EmployeeLicense::class);
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(EmployeeBankAccount::class);
    }

    public function files(): MorphMany
    {
        return $this->morphMany(EmployeeFile::class, 'fileable');
    }
}
