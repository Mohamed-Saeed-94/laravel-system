<?php

namespace Modules\HR\Filament\Resources\EmployeeResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Modules\HR\Filament\Resources\EmployeeResource;

class EditEmployee extends EditRecord
{
    protected static string $resource = EmployeeResource::class;
}
