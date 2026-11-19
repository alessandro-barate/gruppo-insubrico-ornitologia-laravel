<?php

namespace App\Filament\Resources\PublicContributions\Pages;

use App\Filament\Resources\PublicContributions\PublicContributionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPublicContributions extends ListRecords
{
    protected static string $resource = PublicContributionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Aggiungi un anno'),
        ];
    }
}
