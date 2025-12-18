<?php

namespace Modules\Core\Filament\Resources\JobTitleResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Core\Filament\Resources\JobTitleResource;

class ListJobTitles extends ListRecords
{
    protected static string $resource = JobTitleResource::class;

}
