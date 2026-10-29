<?php

namespace App\Filament\Resources\LinkSubsections\Pages;

use App\Filament\Resources\LinkSubsections\LinkSubsectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLinkSubsections extends ListRecords
{
    protected static string $resource = LinkSubsectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
