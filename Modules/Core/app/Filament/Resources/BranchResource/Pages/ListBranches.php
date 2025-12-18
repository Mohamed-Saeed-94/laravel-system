<?php

namespace Modules\Core\Filament\Resources\BranchResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Modules\Core\Filament\Resources\BranchResource;

class ListBranches extends ListRecords
{
    protected static string $resource = BranchResource::class;
    
}
