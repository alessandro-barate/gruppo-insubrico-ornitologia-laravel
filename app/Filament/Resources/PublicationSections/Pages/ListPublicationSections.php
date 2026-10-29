<?php

namespace App\Filament\Resources\PublicationSections\Pages;

use App\Filament\Resources\PublicationSections\PublicationSectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPublicationSections extends ListRecords
{
    protected static string $resource = PublicationSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
