<?php

namespace Modules\HR\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class DepartmentInBranch implements ValidationRule
{
    public function __construct(private ?int $branchId)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->branchId || ! $value) {
            $fail($attribute,__('The selected department is invalid for the chosen branch.'));
            return;
        }

        $exists = DB::table('branch_departments')
            ->join('departments', 'departments.id', '=', 'branch_departments.department_id')
            ->where('branch_departments.branch_id', $this->branchId)
            ->where('branch_departments.department_id', $value)
            ->where('branch_departments.is_active', true)
            ->where('departments.is_active', true)
            ->exists();

        if (! $exists) {
            $fail($attribute,__('The selected department is invalid for the chosen branch.'));
        }
    }
}
