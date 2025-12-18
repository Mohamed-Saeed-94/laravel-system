<?php

return [
    'navigation_label' => 'الموظفين',
    'label' => 'الموظفين',
    'singular' => 'موظف',

    'fields' => [
        'employee_code' => 'رقم الموظف',
        'full_name' => 'الاسم الكامل',
        'email' => 'البريد الإلكتروني',
        'gender' => 'النوع',
        'branch' => 'الفرع',
        'department' => 'القسم',
        'job_title' => 'المسمى الوظيفي',
        'hire_date' => 'تاريخ التعيين',
        'termination_date' => 'تاريخ انتهاء الخدمة',
        'status' => 'الحالة',
        'notes' => 'ملاحظات',
    ],

    'gender' => [
        'male' => 'ذكر',
        'female' => 'أنثى',
    ],

    'status' => [
        'active' => 'على رأس العمل',
        'suspended' => 'موقوف',
        'terminated' => 'منتهي الخدمة',
    ],
];
