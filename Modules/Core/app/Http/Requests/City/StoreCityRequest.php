<?php

namespace Modules\Core\Http\Requests\City;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return backpack_auth()->check();
    }

    public function rules(): array
    {
        return [
            'name_ar' => ['required', 'string', 'max:255', Rule::unique('cities', 'name_ar')],
            'name_en' => ['nullable', 'string', 'max:255', Rule::unique('cities', 'name_en')],
            'is_active' => ['boolean'],
        ];
    }
}
