<?php

namespace Modules\Core\Http\Requests\Admin\Branch;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBranchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return backpack_auth()->check();
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'city_id' => ['required', 'integer', 'exists:cities,id'],
            'name_ar' => ['required', 'string', 'max:255', Rule::unique('branches', 'name_ar')->ignore($id)],
            'name_en' => ['nullable', 'string', 'max:255', Rule::unique('branches', 'name_en')->ignore($id)],
            'address' => ['nullable', 'string'],
            'is_active' => ['boolean'],
        ];
    }
}
