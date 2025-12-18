<?php

namespace Modules\HR\Http\Requests\Admin\EmployeeFile;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Modules\HR\Models\Employee;
use Modules\HR\Models\EmployeeIdentity;
use Modules\HR\Models\EmployeeLicense;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return backpack_auth()->check();
    }

    public function rules(): array
    {
        $fileableType = $this->input('fileable_type');
        $fileableIdRule = ['required', 'integer'];

        if (in_array($fileableType, $this->allowedFileableTypes(), true)) {
            $table = (new $fileableType())->getTable();
            $fileableIdRule[] = Rule::exists($table, 'id');
        }

        $fileRule = $this->isMethod('POST') ? ['required', 'file'] : ['nullable', 'file'];

        return [
            'fileable_type' => ['required', Rule::in($this->allowedFileableTypes())],
            'fileable_id' => $fileableIdRule,
            'category' => ['required', Rule::in(['employee_photo', 'identity_photo', 'license_photo', 'other'])],
            'file_path' => $fileRule,
            'side' => ['nullable', Rule::in(['front', 'back', 'other'])],
            'is_primary' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ];
    }

    /**
     * @return array<class-string>
     */
    private function allowedFileableTypes(): array
    {
        return [
            Employee::class,
            EmployeeIdentity::class,
            EmployeeLicense::class,
        ];
    }
}
