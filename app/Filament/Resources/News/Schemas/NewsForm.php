<?php

namespace App\Filament\Resources\News\Schemas;

use App\Filament\Support\Fields;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class NewsForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Notizia')
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        Fields::title()->columnSpanFull(),
                        Fields::slug(),
                        TextInput::make('category')
                            ->label('Categoria')
                            ->placeholder('Es. Monitoraggio, Escursioni, Corsi')
                            ->maxLength(100),
                        Textarea::make('excerpt')
                            ->label('Sommario')
                            ->helperText('Una o due frasi: compaiono nella card della news e in testa all\'articolo.')
                            ->rows(3)
                            ->columnSpanFull(),
                        Textarea::make('body')
                            ->label('Testo')
                            ->rows(12)
                            ->columnSpanFull(),
                    ]),
                Section::make('Pubblicazione')
                    ->columnSpan(1)
                    ->schema([
                        DatePicker::make('date')
                            ->label('Data')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->default(now())
                            ->required(),
                        Toggle::make('is_published')
                            ->label('Visibile sul sito')
                            ->default(true),
                        Fields::image('image', 'Immagine di copertina', 'news'),
                    ]),
                Section::make('Allegati')
                    ->columnSpanFull()
                    ->schema([
                        Fields::typedLinks('attachments', 'File e link allegati', 'news', 'type'),
                    ]),
            ]);
    }
}
