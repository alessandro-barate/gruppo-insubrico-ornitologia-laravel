<?php

namespace App\Filament\Resources\GalleryImages\Schemas;

use App\Filament\Support\Fields;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class GalleryImageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Hidden::make('gallery')->default('progetti'),
            Fields::image('image_path', 'Immagine', 'progetti/galleria')->required()->columnSpanFull(),
            TextInput::make('alt')->label('Descrizione (per i lettori di schermo)')->required()->maxLength(255),
            TextInput::make('caption')->label('Didascalia (facoltativa)')->maxLength(255),
        ]);
    }
}
