{{-- This file is used for menu items by any Backpack v7 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>


<li class="nav-item nav-heading">Core</li>
@if(backpack_user()->can('view cities'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('cities') }}"><i class="la la-city nav-icon"></i> {{ __('core::cities.label') }}</a></li>
@endif
@if(backpack_user()->can('view branches'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('branches') }}"><i class="la la-building nav-icon"></i> {{ __('core::branches.label') }}</a></li>
@endif
@if(backpack_user()->can('view departments'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('departments') }}"><i class="la la-sitemap nav-icon"></i> {{ __('core::departments.label') }}</a></li>
@endif
@if(backpack_user()->can('view job_titles'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('job-titles') }}"><i class="la la-id-badge nav-icon"></i> {{ __('core::job_titles.label') }}</a></li>
@endif

<li class="nav-item nav-heading">HR</li>
@if(backpack_user()->can('view employees'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('employees') }}"><i class="la la-users nav-icon"></i> {{ __('hr::employees.label') }}</a></li>
@endif
@if(backpack_user()->can('view employee_phones'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('employee-phones') }}"><i class="la la-phone nav-icon"></i> Employee Phones</a></li>
@endif
@if(backpack_user()->can('view employee_identities'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('employee-identities') }}"><i class="la la-id-card nav-icon"></i> Employee Identities</a></li>
@endif
@if(backpack_user()->can('view employee_licenses'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('employee-licenses') }}"><i class="la la-drivers-license nav-icon"></i> Employee Licenses</a></li>
@endif
@if(backpack_user()->can('view employee_bank_accounts'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('employee-bank-accounts') }}"><i class="la la-credit-card nav-icon"></i> Employee Bank Accounts</a></li>
@endif
@if(backpack_user()->can('view employee_files'))
    <li class="nav-item"><a class="nav-link" href="{{ backpack_url('employee-files') }}"><i class="la la-file nav-icon"></i> Employee Files</a></li>
@endif
