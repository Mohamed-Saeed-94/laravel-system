<?php

namespace Modules\HR\Http\Requests\Admin\EmployeeBankAccount;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmployeeBankAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return backpack_auth()->check();
    }

    public function rules(): array
    {
        $employeeId = (int) $this->input('employee_id');

        return [
            'employee_id' => ['required', 'integer', 'exists:employees,id'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'account_holder_name' => ['nullable', 'string', 'max:255'],
            'account_number' => [
                'required',
                'string',
                'max:50',
                Rule::unique('employee_bank_accounts', 'account_number')->where(fn ($query) => $query->where('employee_id', $employeeId)),
            ],
            'iban' => ['nullable', 'string', 'max:34'],
            'swift_code' => ['nullable', 'string', 'max:255'],
            'is_primary' => ['boolean'],
            'is_active' => ['boolean'],
        ];
    }
}
