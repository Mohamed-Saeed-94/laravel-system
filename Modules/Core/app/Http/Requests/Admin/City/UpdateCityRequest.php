<?php

namespace Modules\Core\Http\Requests\Admin\City;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return backpack_auth()->check();
    }

    public function rules(): array
    {
        $id = $this->route('id');

        return [
            'name_ar' => ['required', 'string', 'max:255', Rule::unique('cities', 'name_ar')->ignore($id)],
            'name_en' => ['nullable', 'string', 'max:255', Rule::unique('cities', 'name_en')->ignore($id)],
            'is_active' => ['boolean'],
        ];
    }
}
