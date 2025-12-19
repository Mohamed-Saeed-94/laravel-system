<?php

namespace Modules\HR\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Modules\HR\Http\Requests\Admin\EmployeeIdentity\StoreEmployeeIdentityRequest;
use Modules\HR\Http\Requests\Admin\EmployeeIdentity\UpdateEmployeeIdentityRequest;
use Modules\HR\Models\EmployeeIdentity;

class EmployeeIdentityCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    public function setup(): void
    {
        CRUD::setModel(EmployeeIdentity::class);
        CRUD::setRoute(config('backpack.base.route_prefix', 'admin').'/employee-identities');
        CRUD::setEntityNameStrings('Employee Identity', 'Employee Identities');

        $user = backpack_auth()->user();

        if (! $user?->can('view employee_identities')) {
            CRUD::denyAccess(['list', 'show']);
        }

        if (! $user?->can('create employee_identities')) {
            CRUD::denyAccess(['create']);
        }

        if (! $user?->can('update employee_identities')) {
            CRUD::denyAccess(['update']);
        }

        if (! $user?->can('delete employee_identities')) {
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
                'name' => 'type',
                'label' => 'Type',
                'type' => 'text',
            ],
            [
                'name' => 'number',
                'label' => 'Number',
                'type' => 'text',
            ],
            [
                'name' => 'is_primary',
                'label' => 'Primary',
                'type' => 'boolean',
            ],
            [
                'name' => 'expiry_date',
                'label' => 'Expiry Date',
                'type' => 'date',
            ],
        ]);
    }

    protected function setupCreateOperation(): void
    {
        CRUD::setValidation(StoreEmployeeIdentityRequest::class);

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
                'name' => 'type',
                'label' => 'Type',
                'type' => 'select_from_array',
                'options' => [
                    'iqama' => 'Iqama',
                    'saudi_national_id' => 'Saudi National ID',
                    'passport' => 'Passport',
                ],
            ],
            [
                'name' => 'number',
                'label' => 'Number',
                'type' => 'text',
            ],
            [
                'name' => 'sponsor_name',
                'label' => 'Sponsor Name',
                'type' => 'text',
            ],
            [
                'name' => 'sponsor_id_number',
                'label' => 'Sponsor ID Number',
                'type' => 'text',
            ],
            [
                'name' => 'issue_date',
                'label' => 'Issue Date',
                'type' => 'date',
            ],
            [
                'name' => 'expiry_date',
                'label' => 'Expiry Date',
                'type' => 'date',
            ],
            [
                'name' => 'is_primary',
                'label' => 'Primary',
                'type' => 'checkbox',
                'default' => true,
            ],
        ]);
    }

    protected function setupUpdateOperation(): void
    {
        CRUD::setValidation(UpdateEmployeeIdentityRequest::class);
        $this->setupCreateOperation();
    }
}
