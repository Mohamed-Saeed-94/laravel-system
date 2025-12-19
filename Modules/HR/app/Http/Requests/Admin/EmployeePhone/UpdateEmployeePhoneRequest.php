<?php

namespace Modules\HR\Http\Requests\Admin\EmployeePhone;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmployeePhoneRequest extends FormRequest
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
            'phone' => [
                'required',
                'string',
                'max:30',
                Rule::unique('employee_phones', 'phone')
                    ->where(fn ($query) => $query->where('employee_id', $employeeId))
                    ->ignore($id),
            ],
            'type' => ['required', Rule::in(['personal', 'work', 'emergency'])],
            'is_primary' => ['boolean'],
        ];
    }
}
