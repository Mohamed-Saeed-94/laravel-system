<?php

namespace Modules\Core\Http\Requests\Admin\JobTitle;

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

        return [
            'department_id' => ['required', 'integer', 'exists:departments,id'],
            'name_ar' => ['required', 'string', 'max:255', Rule::unique('job_titles', 'name_ar')->ignore($id)],
            'name_en' => ['nullable', 'string', 'max:255', Rule::unique('job_titles', 'name_en')->ignore($id)],
            'is_active' => ['boolean'],
        ];
    }
}
