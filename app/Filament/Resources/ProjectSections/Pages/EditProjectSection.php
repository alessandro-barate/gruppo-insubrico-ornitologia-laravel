<?php

namespace App\Filament\Resources\ProjectSections\Pages;

use App\Filament\Resources\ProjectSections\ProjectSectionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProjectSection extends EditRecord
{
    protected static string $resource = ProjectSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
