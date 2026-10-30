<?php

namespace App\Filament\Resources\ProjectSections\Pages;

use App\Filament\Resources\ProjectSections\ProjectSectionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateProjectSection extends CreateRecord
{
    protected static ?string $title = 'Nuova sezione';

    protected static string $resource = ProjectSectionResource::class;
}
