<?php

namespace Modules\HR\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\HR\Rules\DepartmentInBranch;
use Modules\HR\Rules\JobTitleInBranch;
use Modules\HR\Rules\JobTitleMatchesDepartment;

class StoreEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return backpack_auth()->check();
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('email') && $this->input('email') === '') {
            $this->merge(['email' => null]);
        }
    }

    public function rules(): array
    {
        $branchId = (int) $this->input('branch_id');
        $departmentId = (int) $this->input('department_id');

        return [
            'employee_code' => [
                'required',
                'string',
                'max:30',
                Rule::unique('employees', 'employee_code'),
            ],
            'full_name' => ['required', 'string', 'max:200'],
            'email' => [
                'nullable',
                'email',
                'max:255',
                Rule::unique('employees', 'email'),
            ],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'branch_id' => ['required', 'integer', 'exists:branches,id'],
            'department_id' => [
                'required',
                'integer',
                'exists:departments,id',
                new DepartmentInBranch($branchId),
            ],
            'job_title_id' => [
                'required',
                'integer',
                'exists:job_titles,id',
                new JobTitleInBranch($branchId),
                new JobTitleMatchesDepartment($departmentId),
            ],
            'hire_date' => ['nullable', 'date'],
            'termination_date' => ['nullable', 'date'],
            'status' => ['required', Rule::in(['active', 'suspended', 'terminated'])],
            'notes' => ['nullable', 'string'],
        ];
    }
}
