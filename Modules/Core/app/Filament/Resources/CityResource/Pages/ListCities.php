<?php

namespace Modules\Core\Filament\Resources\CityResource\Pages;

use Filament\Actions\CreateAction;


use Filament\Resources\Pages\ListRecords;
use Modules\Core\Filament\Resources\CityResource;

class ListCities extends ListRecords
{
    protected static string $resource = CityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
