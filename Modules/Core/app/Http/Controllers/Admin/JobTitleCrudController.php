<?php

namespace Modules\Core\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Modules\Core\Http\Requests\JobTitle\StoreJobTitleRequest;
use Modules\Core\Http\Requests\JobTitle\UpdateJobTitleRequest;
use Modules\Core\Models\Department;
use Modules\Core\Models\JobTitle;

class JobTitleCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    public function setup(): void
    {
        CRUD::setModel(JobTitle::class);
        CRUD::setRoute(config('backpack.base.route_prefix', 'admin').'/job-titles');
        CRUD::setEntityNameStrings(__('core::job_titles.singular'), __('core::job_titles.label'));

        $user = backpack_auth()->user();

        if (! $user?->can('job_titles.view_any')) {
            CRUD::denyAccess(['list', 'show']);
        }

        if (! $user?->can('job_titles.create')) {
            CRUD::denyAccess(['create']);
        }

        if (! $user?->can('job_titles.update')) {
            CRUD::denyAccess(['update']);
        }

        if (! $user?->can('job_titles.delete')) {
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
                'name' => 'department',
                'label' => __('core::job_titles.fields.department'),
                'type' => 'relationship',
                'attribute' => 'name_ar',
            ],
            [
                'name' => 'name_ar',
                'label' => __('core::job_titles.fields.name_ar'),
                'type' => 'text',
            ],
            [
                'name' => 'name_en',
                'label' => __('core::job_titles.fields.name_en'),
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
        CRUD::setValidation(StoreJobTitleRequest::class);

        CRUD::addFields([
            [
                'name' => 'department_id',
                'label' => __('core::job_titles.fields.department'),
                'type' => 'select',
                'entity' => 'department',
                'model' => Department::class,
                'attribute' => 'name_ar',
            ],
            [
                'name' => 'name_ar',
                'label' => __('core::job_titles.fields.name_ar'),
                'type' => 'text',
            ],
            [
                'name' => 'name_en',
                'label' => __('core::job_titles.fields.name_en'),
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
        CRUD::setValidation(UpdateJobTitleRequest::class);
        $this->setupCreateOperation();
    }
}
