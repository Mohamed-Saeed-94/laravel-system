<?php

namespace Modules\HR\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class JobTitleMatchesDepartment implements ValidationRule
{
    public function __construct(private ?int $departmentId)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->departmentId || ! $value) {
            $fail($attribute, __('The selected job title must belong to the chosen department.'));
            return;
        }

        $matches = DB::table('job_titles')
            ->where('id', $value)
            ->where('department_id', $this->departmentId)
            ->where('is_active', true)
            ->exists();

        if (! $matches) {
            $fail($attribute, __('The selected job title must belong to the chosen department.'));
        }
    }
}
