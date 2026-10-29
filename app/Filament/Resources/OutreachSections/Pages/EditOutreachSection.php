<?php

namespace App\Filament\Resources\OutreachSections\Pages;

use App\Filament\Resources\OutreachSections\OutreachSectionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditOutreachSection extends EditRecord
{
    protected static string $resource = OutreachSectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
