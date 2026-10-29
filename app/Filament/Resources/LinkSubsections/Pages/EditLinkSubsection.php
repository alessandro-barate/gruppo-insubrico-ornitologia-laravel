<?php

namespace App\Filament\Resources\LinkSubsections\Pages;

use App\Filament\Resources\LinkSubsections\LinkSubsectionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLinkSubsection extends EditRecord
{
    protected static string $resource = LinkSubsectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
