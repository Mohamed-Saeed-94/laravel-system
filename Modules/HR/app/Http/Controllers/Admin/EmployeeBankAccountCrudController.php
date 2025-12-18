<?php

namespace Modules\HR\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Modules\HR\Http\Requests\Admin\EmployeeBankAccount\StoreRequest;
use Modules\HR\Http\Requests\Admin\EmployeeBankAccount\UpdateRequest;
use Modules\HR\Models\EmployeeBankAccount;

class EmployeeBankAccountCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    public function setup(): void
    {
        CRUD::setModel(EmployeeBankAccount::class);
        CRUD::setRoute(config('backpack.base.route_prefix', 'admin').'/employee-bank-accounts');
        CRUD::setEntityNameStrings('Employee Bank Account', 'Employee Bank Accounts');

        $user = backpack_auth()->user();

        if (! $user?->can('view employee_bank_accounts')) {
            CRUD::denyAccess(['list', 'show']);
        }

        if (! $user?->can('create employee_bank_accounts')) {
            CRUD::denyAccess(['create']);
        }

        if (! $user?->can('update employee_bank_accounts')) {
            CRUD::denyAccess(['update']);
        }

        if (! $user?->can('delete employee_bank_accounts')) {
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
                'name' => 'bank_name',
                'label' => 'Bank Name',
                'type' => 'text',
            ],
            [
                'name' => 'account_number',
                'label' => 'Account Number',
                'type' => 'text',
            ],
            [
                'name' => 'iban',
                'label' => 'IBAN',
                'type' => 'text',
            ],
            [
                'name' => 'is_primary',
                'label' => 'Primary',
                'type' => 'boolean',
            ],
            [
                'name' => 'is_active',
                'label' => 'Active',
                'type' => 'boolean',
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
                'name' => 'bank_name',
                'label' => 'Bank Name',
                'type' => 'text',
            ],
            [
                'name' => 'account_holder_name',
                'label' => 'Account Holder Name',
                'type' => 'text',
            ],
            [
                'name' => 'account_number',
                'label' => 'Account Number',
                'type' => 'text',
            ],
            [
                'name' => 'iban',
                'label' => 'IBAN',
                'type' => 'text',
            ],
            [
                'name' => 'swift_code',
                'label' => 'SWIFT Code',
                'type' => 'text',
            ],
            [
                'name' => 'is_primary',
                'label' => 'Primary',
                'type' => 'checkbox',
                'default' => false,
            ],
            [
                'name' => 'is_active',
                'label' => 'Active',
                'type' => 'checkbox',
                'default' => true,
            ],
        ]);
    }

    protected function setupUpdateOperation(): void
    {
        CRUD::setValidation(UpdateRequest::class);
        $this->setupCreateOperation();
    }
}
