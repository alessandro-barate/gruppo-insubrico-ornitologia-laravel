<?php

namespace App\Filament\Resources\PublicationSections\Pages;

use App\Filament\Resources\PublicationSections\PublicationSectionResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePublicationSection extends CreateRecord
{
    protected static ?string $title = 'Nuova sezione di Pubblicazioni';

    protected static string $resource = PublicationSectionResource::class;
}
