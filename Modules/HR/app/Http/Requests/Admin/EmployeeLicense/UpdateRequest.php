<?php

namespace Modules\HR\Http\Requests\Admin\EmployeeLicense;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return backpack_auth()->check();
    }

    public function rules(): array
    {
        $id = $this->route('id');
        $employeeId = (int) $this->input('employee_id');

        return [
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'type' => ['required', Rule::in(['private', 'motorcycle', 'public_transport', 'other'])],
            'number' => [
                'required',
                'string',
                'max:30',
                Rule::unique('employee_licenses', 'number')
                    ->where(fn ($query) => $query->where('employee_id', $employeeId))
                    ->ignore($id),
            ],
            'issue_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date'],
            'is_primary' => ['boolean'],
        ];
    }
}
