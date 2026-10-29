<?php

namespace App\Filament\Resources\PageContents\Schemas;

use App\Filament\Resources\PageContents\PageContentResource;
use App\Filament\Support\Fields;
use App\Models\PageContent;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PageContentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(fn (?PageContent $record) => $record
                    ? PageContentResource::pageName($record->page).' › '.$record->label
                    : 'Testo')
                ->description('Lascia vuoto per usare il testo predefinito del sito.')
                ->columnSpanFull()
                // Un solo campo, del tipo giusto per questo testo
                ->schema(fn (?PageContent $record) => [
                    match ($record?->type) {
                        'html' => Fields::richText('value', 'Testo')->hiddenLabel(),
                        'image' => Fields::image('value', 'Immagine', 'pagine')->hiddenLabel(),
                        default => TextInput::make('value')->hiddenLabel()->maxLength(500),
                    },
                ]),
        ]);
    }
}
