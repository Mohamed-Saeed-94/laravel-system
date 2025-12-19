<?php

namespace Modules\Core\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Modules\Core\Http\Requests\Admin\Department\StoreDepartmentRequest;
use Modules\Core\Http\Requests\Admin\Department\UpdateDepartmentRequest;
use Modules\Core\Models\Department;

class DepartmentCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    public function setup(): void
    {
        CRUD::setModel(Department::class);
        CRUD::setRoute(config('backpack.base.route_prefix', 'admin').'/departments');
        CRUD::setEntityNameStrings(__('core::departments.singular'), __('core::departments.label'));

        $user = backpack_auth()->user();

        if (! $user?->can('view departments')) {
            CRUD::denyAccess(['list', 'show']);
        }

        if (! $user?->can('create departments')) {
            CRUD::denyAccess(['create']);
        }

        if (! $user?->can('update departments')) {
            CRUD::denyAccess(['update']);
        }

        if (! $user?->can('delete departments')) {
            CRUD::denyAccess(['delete']);
        }
    }

    protected function setupListOperation(): void
    {
        CRUD::addColumns([
            [
                'name' => 'id',
                'label' => __('core::fields.id'),
                'type' => 'number',
            ],
            [
                'name' => 'name_ar',
                'label' => __('core::departments.fields.name_ar'),
                'type' => 'text',
            ],
            [
                'name' => 'name_en',
                'label' => __('core::departments.fields.name_en'),
                'type' => 'text',
            ],
            [
                'name' => 'is_active',
                'label' => __('core::fields.is_active'),
                'type' => 'boolean',
            ],
            [
                'name' => 'created_at',
                'label' => __('core::fields.created_at'),
                'type' => 'datetime',
            ],
        ]);

        $this->crud->addFilter([
            'name' => 'is_active',
            'label' => __('core::filters.status'),
            'type' => 'dropdown',
        ], [
            1 => __('core::filters.active'),
            0 => __('core::filters.inactive'),
        ], function ($value) {
            $this->crud->addClause('where', 'is_active', $value);
        });
    }

    protected function setupCreateOperation(): void
    {
        CRUD::setValidation(StoreDepartmentRequest::class);

        CRUD::addFields([
            [
                'name' => 'name_ar',
                'label' => __('core::departments.fields.name_ar'),
                'type' => 'text',
            ],
            [
                'name' => 'name_en',
                'label' => __('core::departments.fields.name_en'),
                'type' => 'text',
            ],
            [
                'name' => 'is_active',
                'label' => __('core::fields.is_active'),
                'type' => 'checkbox',
                'default' => true,
            ],
        ]);
    }

    protected function setupUpdateOperation(): void
    {
        CRUD::setValidation(UpdateDepartmentRequest::class);
        $this->setupCreateOperation();
    }
}
