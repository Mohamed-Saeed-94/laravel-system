<?php

namespace Modules\HR\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Modules\HR\Http\Requests\Admin\EmployeePhone\StoreRequest;
use Modules\HR\Http\Requests\Admin\EmployeePhone\UpdateRequest;
use Modules\HR\Models\EmployeePhone;

class EmployeePhoneCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    public function setup(): void
    {
        CRUD::setModel(EmployeePhone::class);
        CRUD::setRoute(config('backpack.base.route_prefix', 'admin').'/employee-phones');
        CRUD::setEntityNameStrings('Employee Phone', 'Employee Phones');

        $user = backpack_auth()->user();

        if (! $user?->can('view employee_phones')) {
            CRUD::denyAccess(['list', 'show']);
        }

        if (! $user?->can('create employee_phones')) {
            CRUD::denyAccess(['create']);
        }

        if (! $user?->can('update employee_phones')) {
            CRUD::denyAccess(['update']);
        }

        if (! $user?->can('delete employee_phones')) {
            CRUD::denyAccess(['delete']);
        }
    }

    protected function setupListOperation(): void
    {
        CRUD::addColumns([
            [
                'name' => 'employee',
                'label' => 'Employee',
                'type' => 'relationship',
                'attribute' => 'full_name',
            ],
            [
                'name' => 'phone',
                'label' => 'Phone',
                'type' => 'text',
            ],
            [
                'name' => 'type',
                'label' => 'Type',
                'type' => 'text',
            ],
            [
                'name' => 'is_primary',
                'label' => 'Primary',
                'type' => 'boolean',
            ],
            [
                'name' => 'created_at',
                'label' => 'Created At',
                'type' => 'datetime',
            ],
        ]);
    }

    protected function setupCreateOperation(): void
    {
        CRUD::setValidation(StoreRequest::class);

        CRUD::addFields([
            [
                'name' => 'employee_id',
                'label' => 'Employee',
                'type' => 'select',
                'entity' => 'employee',
                'model' => \Modules\HR\Models\Employee::class,
                'attribute' => 'full_name',
            ],
            [
                'name' => 'phone',
                'label' => 'Phone',
                'type' => 'text',
            ],
            [
                'name' => 'type',
                'label' => 'Type',
                'type' => 'select_from_array',
                'options' => [
                    'personal' => 'Personal',
                    'work' => 'Work',
                    'emergency' => 'Emergency',
                ],
                'default' => 'personal',
            ],
            [
                'name' => 'is_primary',
                'label' => 'Primary',
                'type' => 'checkbox',
                'default' => false,
            ],
        ]);
    }

    protected function setupUpdateOperation(): void
    {
        CRUD::setValidation(UpdateRequest::class);
        $this->setupCreateOperation();
    }
}
