<?php

namespace App\Filament\Resources\PublicContributions\Pages;

use App\Filament\Resources\PublicContributions\PublicContributionResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePublicContribution extends CreateRecord
{
    protected static ?string $title = 'Nuovo anno';

    protected static string $resource = PublicContributionResource::class;
}
