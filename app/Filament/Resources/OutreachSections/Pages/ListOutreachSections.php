<?php

namespace App\Filament\Resources\OutreachSections\Pages;

use App\Filament\Resources\OutreachSections\OutreachSectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListOutreachSections extends ListRecords
{
    protected static string $resource = OutreachSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
