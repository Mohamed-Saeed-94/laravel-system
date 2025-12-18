<?php

namespace Modules\Core\Http\Requests\JobTitle;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobTitleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return backpack_auth()->check();
    }

    public function rules(): array
    {
        return [
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'name_ar' => ['required', 'string', 'max:255', Rule::unique('job_titles', 'name_ar')],
            'name_en' => ['nullable', 'string', 'max:255', Rule::unique('job_titles', 'name_en')],
            'is_active' => ['boolean'],
        ];
    }
}
