<?php

namespace Modules\Core\Filament\Resources\DepartmentResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Modules\Core\Filament\Resources\DepartmentResource;

class ListDepartments extends ListRecords
{
    protected static string $resource = DepartmentResource::class;
}
