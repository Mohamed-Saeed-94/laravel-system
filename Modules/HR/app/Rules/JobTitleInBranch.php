<?php

namespace Modules\HR\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;

class JobTitleInBranch implements ValidationRule
{
    public function __construct(private ?int $branchId)
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->branchId || ! $value) {
            return;
        }

        $exists = DB::table('branch_job_titles')
            ->join('job_titles', 'job_titles.id', '=', 'branch_job_titles.job_title_id')
            ->where('branch_job_titles.branch_id', $this->branchId)
            ->where('branch_job_titles.job_title_id', $value)
            ->where('branch_job_titles.is_active', true)
            ->where('job_titles.is_active', true)
            ->exists();

        if (! $exists) {
            $fail($attribute,__('The selected job title is invalid for the chosen branch.'));
        }
    }
}
