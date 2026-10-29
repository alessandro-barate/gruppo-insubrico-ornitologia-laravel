<?php

namespace App\Filament\Resources\ProjectSections\Pages;

use App\Filament\Resources\ProjectSections\ProjectSectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProjectSections extends ListRecords
{
    protected static string $resource = ProjectSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
