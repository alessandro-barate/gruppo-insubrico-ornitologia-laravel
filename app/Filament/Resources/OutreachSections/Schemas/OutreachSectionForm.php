<?php

namespace App\Filament\Resources\OutreachSections\Schemas;

use App\Filament\Support\Fields;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class OutreachSectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Sottosezione')
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        Fields::title()->columnSpanFull(),
                        Fields::slug()->unique(ignoreRecord: true),
                        TextInput::make('intro_excerpt')->label('Testo breve della card')->maxLength(255),
                        Fields::richText('intro_text', 'Testo introduttivo'),
                    ]),
                Section::make('Card')
                    ->columnSpan(1)
                    ->schema([
                        Fields::image('image_path', 'Immagine della card', 'divulgazione'),
                        TagsInput::make('title_parts')
                            ->label('Righe del titolo nella card')
                            ->placeholder('Scrivi una riga e premi Invio')
                            ->helperText('Es. "Corsi di", "Birdwatching", "e Ornitologia". Vuoto = titolo su una riga.')
                            ->reorderable(),
                        TextInput::make('aria_label')
                            ->label('Descrizione del pulsante (accessibilità)')
                            ->maxLength(255),
                    ]),
            ]);
    }
}
