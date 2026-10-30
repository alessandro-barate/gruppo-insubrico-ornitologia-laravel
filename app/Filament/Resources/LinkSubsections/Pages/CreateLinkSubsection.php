<?php

namespace App\Filament\Resources\LinkSubsections\Pages;

use App\Filament\Resources\LinkSubsections\LinkSubsectionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLinkSubsection extends CreateRecord
{
    protected static ?string $title = 'Nuova colonna di link';

    protected static string $resource = LinkSubsectionResource::class;
}
