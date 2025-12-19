<?php

namespace Modules\Core\Http\Requests\Admin\Department;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return backpack_auth()->check();
    }

    public function rules(): array
    {
        return [
            'name_ar' => ['required', 'string', 'max:255', Rule::unique('departments', 'name_ar')],
            'name_en' => ['nullable', 'string', 'max:255', Rule::unique('departments', 'name_en')],
            'is_active' => ['boolean'],
        ];
    }
}
