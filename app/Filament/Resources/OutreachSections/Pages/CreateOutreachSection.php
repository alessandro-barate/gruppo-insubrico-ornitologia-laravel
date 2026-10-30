<?php

namespace App\Filament\Resources\OutreachSections\Pages;

use App\Filament\Resources\OutreachSections\OutreachSectionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateOutreachSection extends CreateRecord
{
    protected static ?string $title = 'Nuova sottosezione di Divulgazione';

    protected static string $resource = OutreachSectionResource::class;
}
