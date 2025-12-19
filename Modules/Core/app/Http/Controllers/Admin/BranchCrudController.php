<?php

namespace Modules\Core\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Modules\Core\Http\Requests\Admin\Branch\StoreBranchRequest;
use Modules\Core\Http\Requests\Admin\Branch\UpdateBranchRequest;
use Modules\Core\Models\Branch;
use Modules\Core\Models\City;

class BranchCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    public function setup(): void
    {
        CRUD::setModel(Branch::class);
        CRUD::setRoute(config('backpack.base.route_prefix', 'admin').'/branches');
        CRUD::setEntityNameStrings(__('core::branches.singular'), __('core::branches.label'));

        $user = backpack_auth()->user();

        if (! $user?->can('view branches')) {
            CRUD::denyAccess(['list', 'show']);
        }

        if (! $user?->can('create branches')) {
            CRUD::denyAccess(['create']);
        }

        if (! $user?->can('update branches')) {
            CRUD::denyAccess(['update']);
        }

        if (! $user?->can('delete branches')) {
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
                'name' => 'city',
                'label' => __('core::branches.fields.city'),
                'type' => 'relationship',
                'attribute' => 'name_ar',
            ],
            [
                'name' => 'name_ar',
                'label' => __('core::branches.fields.name_ar'),
                'type' => 'text',
            ],
            [
                'name' => 'name_en',
                'label' => __('core::branches.fields.name_en'),
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
        CRUD::setValidation(StoreBranchRequest::class);

        CRUD::addFields([
            [
                'name' => 'city_id',
                'label' => __('core::branches.fields.city'),
                'type' => 'select',
                'entity' => 'city',
                'model' => City::class,
                'attribute' => 'name_ar',
            ],
            [
                'name' => 'name_ar',
                'label' => __('core::branches.fields.name_ar'),
                'type' => 'text',
            ],
            [
                'name' => 'name_en',
                'label' => __('core::branches.fields.name_en'),
                'type' => 'text',
            ],
            [
                'name' => 'address',
                'label' => __('core::branches.fields.address'),
                'type' => 'textarea',
                'attributes' => ['rows' => 3],
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
        CRUD::setValidation(UpdateBranchRequest::class);
        $this->setupCreateOperation();
    }
}
