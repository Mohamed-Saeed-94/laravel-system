<?php

namespace Modules\HR\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Modules\Core\Models\Branch;
use Modules\Core\Models\Department;
use Modules\Core\Models\JobTitle;
use Modules\HR\Http\Requests\Admin\Employee\StoreRequest;
use Modules\HR\Http\Requests\Admin\Employee\UpdateRequest;
use Modules\HR\Models\Employee;

class EmployeeCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;

    public function setup(): void
    {
        CRUD::setModel(Employee::class);
        CRUD::setRoute(config('backpack.base.route_prefix', 'admin').'/employees');
        CRUD::setEntityNameStrings(__('hr::employees.singular'), __('hr::employees.label'));

        $user = backpack_auth()->user();

        if (! $user?->can('view employees')) {
            CRUD::denyAccess(['list', 'show']);
        }

        if (! $user?->can('create employees')) {
            CRUD::denyAccess(['create']);
        }

        if (! $user?->can('update employees')) {
            CRUD::denyAccess(['update']);
        }

        if (! $user?->can('delete employees')) {
            CRUD::denyAccess(['delete']);
        }
    }

    protected function setupListOperation(): void
    {
        CRUD::addColumns([
            [
                'name' => 'employee_code',
                'label' => __('hr::employees.fields.employee_code'),
                'type' => 'text',
            ],
            [
                'name' => 'full_name',
                'label' => __('hr::employees.fields.full_name'),
                'type' => 'text',
            ],
            [
                'name' => 'email',
                'label' => __('hr::employees.fields.email'),
                'type' => 'text',
            ],
            [
                'name' => 'branch',
                'label' => __('hr::employees.fields.branch'),
                'type' => 'relationship',
                'attribute' => 'name_ar',
            ],
            [
                'name' => 'department',
                'label' => __('hr::employees.fields.department'),
                'type' => 'relationship',
                'attribute' => 'name_ar',
            ],
            [
                'name' => 'jobTitle',
                'label' => __('hr::employees.fields.job_title'),
                'type' => 'relationship',
                'attribute' => 'name_ar',
            ],
            [
                'name' => 'status',
                'label' => __('hr::employees.fields.status'),
                'type' => 'text',
            ],
            [
                'name' => 'hire_date',
                'label' => __('hr::employees.fields.hire_date'),
                'type' => 'date',
            ],
        ]);

        $this->crud->addFilter([
            'name' => 'branch_id',
            'label' => __('hr::employees.fields.branch'),
            'type' => 'select2',
        ], Branch::query()->pluck('name_ar', 'id')->toArray(), function ($value) {
            $this->crud->addClause('where', 'branch_id', $value);
        });

        $this->crud->addFilter([
            'name' => 'department_id',
            'label' => __('hr::employees.fields.department'),
            'type' => 'select2',
        ], Department::query()->pluck('name_ar', 'id')->toArray(), function ($value) {
            $this->crud->addClause('where', 'department_id', $value);
        });

        $this->crud->addFilter([
            'name' => 'job_title_id',
            'label' => __('hr::employees.fields.job_title'),
            'type' => 'select2',
        ], JobTitle::query()->pluck('name_ar', 'id')->toArray(), function ($value) {
            $this->crud->addClause('where', 'job_title_id', $value);
        });

        $this->crud->addFilter([
            'name' => 'status',
            'label' => __('hr::employees.fields.status'),
            'type' => 'dropdown',
        ], [
            'active' => __('hr::employees.status.active'),
            'suspended' => __('hr::employees.status.suspended'),
            'terminated' => __('hr::employees.status.terminated'),
        ], function ($value) {
            $this->crud->addClause('where', 'status', $value);
        });

    }

    protected function setupCreateOperation(): void
    {
        CRUD::setValidation(StoreRequest::class);

        CRUD::addFields([
            [
                'name' => 'employee_code',
                'label' => __('hr::employees.fields.employee_code'),
                'type' => 'text',
            ],
            [
                'name' => 'full_name',
                'label' => __('hr::employees.fields.full_name'),
                'type' => 'text',
            ],
            [
                'name' => 'email',
                'label' => __('hr::employees.fields.email'),
                'type' => 'email',
            ],
            [
                'name' => 'gender',
                'label' => __('hr::employees.fields.gender'),
                'type' => 'select_from_array',
                'options' => [
                    'male' => __('hr::employees.gender.male'),
                    'female' => __('hr::employees.gender.female'),
                ],
                'allows_null' => true,
            ],
            [
                'name' => 'branch_id',
                'label' => __('hr::employees.fields.branch'),
                'type' => 'select',
                'entity' => 'branch',
                'model' => Branch::class,
                'attribute' => 'name_ar',
                'options' => fn ($query) => $query->where('is_active', true)->orderBy('name_ar')->get(),
            ],
            [
                'name' => 'department_id',
                'label' => __('hr::employees.fields.department'),
                'type' => 'select',
                'entity' => 'department',
                'model' => Department::class,
                'attribute' => 'name_ar',
                'options' => function ($query) {
                    $branchId = (int) request()->input('branch_id');

                    return $query
                        ->where('departments.is_active', true)
                        ->when($branchId, function ($q) use ($branchId) {
                            $q->whereIn('departments.id', function ($builder) use ($branchId) {
                                $builder->select('department_id')
                                    ->from('branch_departments')
                                    ->where('branch_id', $branchId)
                                    ->where('is_active', true);
                            });
                        })
                        ->orderBy('name_ar')
                        ->get();
                },
            ],
            [
                'name' => 'job_title_id',
                'label' => __('hr::employees.fields.job_title'),
                'type' => 'select',
                'entity' => 'jobTitle',
                'model' => JobTitle::class,
                'attribute' => 'name_ar',
                'options' => function ($query) {
                    $branchId = (int) request()->input('branch_id');
                    $departmentId = (int) request()->input('department_id');

                    return $query
                        ->where('job_titles.is_active', true)
                        ->when($branchId, function ($q) use ($branchId) {
                            $q->whereIn('job_titles.id', function ($builder) use ($branchId) {
                                $builder->select('job_title_id')
                                    ->from('branch_job_titles')
                                    ->where('branch_id', $branchId)
                                    ->where('is_active', true);
                            });
                        })
                        ->when($departmentId, fn ($q) => $q->where('department_id', $departmentId))
                        ->orderBy('name_ar')
                        ->get();
                },
            ],
            [
                'name' => 'hire_date',
                'label' => __('hr::employees.fields.hire_date'),
                'type' => 'date',
            ],
            [
                'name' => 'termination_date',
                'label' => __('hr::employees.fields.termination_date'),
                'type' => 'date',
            ],
            [
                'name' => 'status',
                'label' => __('hr::employees.fields.status'),
                'type' => 'select_from_array',
                'options' => [
                    'active' => __('hr::employees.status.active'),
                    'suspended' => __('hr::employees.status.suspended'),
                    'terminated' => __('hr::employees.status.terminated'),
                ],
                'default' => 'active',
            ],
            [
                'name' => 'notes',
                'label' => __('hr::employees.fields.notes'),
                'type' => 'textarea',
            ],
        ]);
    }

    protected function setupUpdateOperation(): void
    {
        CRUD::setValidation(UpdateRequest::class);
        $this->setupCreateOperation();
    }
}
