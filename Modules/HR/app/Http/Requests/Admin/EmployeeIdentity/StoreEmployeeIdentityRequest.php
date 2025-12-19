<?php

namespace Modules\HR\Http\Requests\Admin\EmployeeIdentity;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeIdentityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return backpack_auth()->check();
    }

    public function rules(): array
    {
        $type = $this->input('type');

        return [
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'type' => ['required', Rule::in(['iqama', 'saudi_national_id', 'passport'])],
            'number' => [
                'required',
                'string',
                'max:30',
                Rule::unique('employee_identities', 'number')->where(fn ($query) => $query->where('type', $type)),
            ],
            'sponsor_name' => ['nullable', 'string', 'max:200'],
            'sponsor_id_number' => ['nullable', 'string', 'max:30'],
            'issue_date' => ['nullable', 'date'],
            'expiry_date' => ['nullable', 'date'],
            'is_primary' => ['boolean'],
        ];
    }
}
