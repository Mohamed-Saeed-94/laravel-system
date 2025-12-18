<?php

return [
    'navigation_label' => 'Employees',
    'label' => 'Employees',
    'singular' => 'Employee',

    'fields' => [
        'employee_code' => 'Employee Code',
        'full_name' => 'Full Name',
        'email' => 'Email',
        'gender' => 'Gender',
        'branch' => 'Branch',
        'department' => 'Department',
        'job_title' => 'Job Title',
        'hire_date' => 'Hire Date',
        'termination_date' => 'Termination Date',
        'status' => 'Status',
        'notes' => 'Notes',
    ],

    'gender' => [
        'male' => 'Male',
        'female' => 'Female',
    ],

    'status' => [
        'active' => 'Active',
        'suspended' => 'Suspended',
        'terminated' => 'Terminated',
    ],
];
