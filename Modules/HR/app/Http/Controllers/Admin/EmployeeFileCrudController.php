<?php

namespace Modules\HR\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Modules\HR\Http\Requests\Admin\EmployeeFile\StoreRequest;
use Modules\HR\Http\Requests\Admin\EmployeeFile\UpdateRequest;
use Modules\HR\Models\Employee;
use Modules\HR\Models\EmployeeFile;
use Modules\HR\Models\EmployeeIdentity;
use Modules\HR\Models\EmployeeLicense;

class EmployeeFileCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    public function setup(): void
    {
        CRUD::setModel(EmployeeFile::class);
        CRUD::setRoute(config('backpack.base.route_prefix', 'admin').'/employee-files');
        CRUD::setEntityNameStrings('Employee File', 'Employee Files');

        $user = backpack_auth()->user();

        if (! $user?->can('view employee_files')) {
            CRUD::denyAccess(['list', 'show']);
        }

        if (! $user?->can('create employee_files')) {
            CRUD::denyAccess(['create']);
        }

        if (! $user?->can('update employee_files')) {
            CRUD::denyAccess(['update']);
        }

        if (! $user?->can('delete employee_files')) {
            CRUD::denyAccess(['delete']);
        }
    }

    protected function setupListOperation(): void
    {
        CRUD::addColumns([
            [
                'name' => 'fileable_type',
                'label' => 'Attached To',
                'type' => 'text',
            ],
            [
                'name' => 'fileable_id',
                'label' => 'Related ID',
                'type' => 'number',
            ],
            [
                'name' => 'category',
                'label' => 'Category',
                'type' => 'text',
            ],
            [
                'name' => 'file_name',
                'label' => 'File Name',
                'type' => 'text',
            ],
            [
                'name' => 'mime_type',
                'label' => 'MIME Type',
                'type' => 'text',
            ],
            [
                'name' => 'file_size',
                'label' => 'Size (bytes)',
                'type' => 'number',
            ],
            [
                'name' => 'side',
                'label' => 'Side',
                'type' => 'text',
            ],
            [
                'name' => 'is_primary',
                'label' => 'Primary',
                'type' => 'boolean',
            ],
        ]);
    }

    protected function setupCreateOperation(): void
    {
        CRUD::setValidation(StoreRequest::class);

        CRUD::addFields([
            [
                'name' => 'fileable_type',
                'label' => 'Related Model',
                'type' => 'select_from_array',
                'options' => $this->fileableOptions(),
            ],
            [
                'name' => 'fileable_id',
                'label' => 'Related Record',
                'type' => 'number',
            ],
            [
                'name' => 'category',
                'label' => 'Category',
                'type' => 'select_from_array',
                'options' => [
                    'employee_photo' => 'Employee Photo',
                    'identity_photo' => 'Identity Photo',
                    'license_photo' => 'License Photo',
                    'other' => 'Other',
                ],
            ],
            [
                'name' => 'file_path',
                'label' => 'File',
                'type' => 'upload',
                'upload' => true,
                'disk' => 'public',
                'path' => 'employee-files',
            ],
            [
                'name' => 'side',
                'label' => 'Side',
                'type' => 'select_from_array',
                'options' => [
                    'front' => 'Front',
                    'back' => 'Back',
                    'other' => 'Other',
                ],
                'allows_null' => true,
            ],
            [
                'name' => 'is_primary',
                'label' => 'Primary',
                'type' => 'checkbox',
                'default' => false,
            ],
            [
                'name' => 'notes',
                'label' => 'Notes',
                'type' => 'textarea',
            ],
        ]);
    }

    protected function setupUpdateOperation(): void
    {
        CRUD::setValidation(UpdateRequest::class);
        $this->setupCreateOperation();
    }

    private function fileableOptions(): array
    {
        return [
            Employee::class => 'Employee',
            EmployeeIdentity::class => 'Employee Identity',
            EmployeeLicense::class => 'Employee License',
        ];
    }
}
