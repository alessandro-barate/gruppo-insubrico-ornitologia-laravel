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
                ->description(fn (?PageContent $record) => $record?->type === 'image'
                    ? 'Lascia vuoto per usare l\'immagine predefinita del sito.'
                    : 'Lascia vuoto per usare il testo predefinito del sito.')
                ->columnSpanFull()
                // Un solo campo, del tipo giusto per questo testo
                ->schema(fn (?PageContent $record) => [
                    match ($record?->type) {
                        'html' => Fields::richText('value', 'Testo')->hiddenLabel(),
                        'image' => Fields::image('value', 'Immagine', 'pagine')
                            ->hiddenLabel()
                            ->helperText(self::imageHelp($record)),
                        default => TextInput::make('value')->hiddenLabel()->maxLength(500),
                    },
                ]),
        ]);
    }

    /** Consiglio sul formato, secondo il punto del sito in cui va l'immagine. */
    private static function imageHelp(?PageContent $record): string
    {
        $key = $record?->key ?? '';
        $tips = match (true) {
            str_starts_with($key, 'hero') || in_array($key, ['activities_image', 'join_background', 'social_image', 'footer_image', 'menu_image'], true)
                => 'Immagine orizzontale e larga, almeno 1920 px: occupa tutta la larghezza dello schermo e viene ritagliata. Tieni il soggetto al centro.',
            str_starts_with($key, 'card_') || str_starts_with($key, 'activity_') || $key === 'join_card_image'
                => 'Immagine quadrata o quasi, almeno 800 px di lato: viene ritagliata per riempire la card.',
            str_starts_with($key, 'panel_')
                => 'Immagine verticale o quadrata: il pannello è stretto quando è chiuso e si allarga quando è aperto.',
            default => 'L\'immagine si adatta alla larghezza della pagina.',
        };

        return $tips.' JPG, PNG o WebP, al massimo 8 MB.';
    }
}
