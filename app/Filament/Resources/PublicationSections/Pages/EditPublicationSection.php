<?php

namespace App\Filament\Resources\PublicationSections\Pages;

use App\Filament\Resources\PublicationSections\PublicationSectionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPublicationSection extends EditRecord
{
    protected static string $resource = PublicationSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
