<?php

namespace Modules\Core\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Modules\Core\Http\Requests\City\StoreCityRequest;
use Modules\Core\Http\Requests\City\UpdateCityRequest;
use Modules\Core\Models\City;

class CityCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    public function setup(): void
    {
        CRUD::setModel(City::class);
        CRUD::setRoute(config('backpack.base.route_prefix', 'admin').'/cities');
        CRUD::setEntityNameStrings(__('core::cities.singular'), __('core::cities.label'));

        $user = backpack_auth()->user();

        if (! $user?->can('cities.view_any')) {
            CRUD::denyAccess(['list', 'show']);
        }

        if (! $user?->can('cities.create')) {
            CRUD::denyAccess(['create']);
        }

        if (! $user?->can('cities.update')) {
            CRUD::denyAccess(['update']);
        }

        if (! $user?->can('cities.delete')) {
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
                'label' => __('core::cities.fields.name_ar'),
                'type' => 'text',
            ],
            [
                'name' => 'name_en',
                'label' => __('core::cities.fields.name_en'),
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
        CRUD::setValidation(StoreCityRequest::class);

        CRUD::addFields([
            [
                'name' => 'name_ar',
                'label' => __('core::cities.fields.name_ar'),
                'type' => 'text',
            ],
            [
                'name' => 'name_en',
                'label' => __('core::cities.fields.name_en'),
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
        CRUD::setValidation(UpdateCityRequest::class);
        $this->setupCreateOperation();
    }
}
