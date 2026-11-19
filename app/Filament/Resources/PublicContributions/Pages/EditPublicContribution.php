<?php

namespace App\Filament\Resources\PublicContributions\Pages;

use App\Filament\Resources\PublicContributions\PublicContributionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPublicContribution extends EditRecord
{
    protected static string $resource = PublicContributionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
