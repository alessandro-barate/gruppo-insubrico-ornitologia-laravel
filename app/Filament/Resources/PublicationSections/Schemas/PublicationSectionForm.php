<?php

namespace App\Filament\Resources\PublicationSections\Schemas;

use App\Filament\Support\Fields;
use App\Models\PublicationSection;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class PublicationSectionForm
{
    public static function configure(Schema $schema): Schema
    {
        $is = fn (string ...$types) => fn (Get $get) => in_array($get('type'), $types, true);

        return $schema
            ->columns(3)
            ->components([
                Section::make('Sezione')
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        Fields::title()->columnSpanFull(),
                        Fields::slug()
                            ->unique(
                                ignoreRecord: true,
                                modifyRuleUsing: fn (Unique $rule, Get $get) => $rule->where('parent_id', $get('parent_id')),
                            ),
                        Select::make('type')
                            ->label('Tipo di pagina')
                            ->options(PublicationSection::TYPES)
                            ->required()
                            ->live()
                            ->disabledOn('edit')
                            ->helperText('Non si cambia dopo la creazione: decide cosa contiene la pagina.'),
                        Select::make('parent_id')
                            ->label('Dentro la sezione')
                            ->placeholder('— Nessuna: è una card della pagina Pubblicazioni —')
                            ->options(fn (?PublicationSection $record) => PublicationSection::where('type', 'group')
                                ->when($record, fn ($q) => $q->whereKeyNot($record->id))
                                ->with('parent')->get()
                                ->mapWithKeys(fn (PublicationSection $s) => [$s->id => $s->fullTitle()]))
                            ->columnSpanFull(),
                        TextInput::make('intro_excerpt')
                            ->label('Testo breve della card')
                            ->maxLength(255)
                            ->columnSpanFull(),
                        Fields::richText('intro_text', 'Testo introduttivo della pagina'),
                        TextInput::make('second_intro_text')
                            ->label('Titolo sopra la lista')
                            ->placeholder('Es. Bibliografia del G.I.O.')
                            ->visible($is('bibliography', 'pdf-list', 'cards'))
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ]),
                Section::make('Immagini e lista')
                    ->columnSpan(1)
                    ->schema([
                        Fields::image('image_path', 'Immagine della card', 'pubblicazioni'),
                        Fields::image('header_image', 'Immagine in testa alla pagina', 'pubblicazioni')
                            ->visible($is('pdf-list', 'bibliography', 'cards', 'group')),
                        TextInput::make('items_per_page')
                            ->label('Voci per pagina')
                            ->numeric()->minValue(1)->maxValue(100)
                            ->placeholder('4')
                            ->visible($is('pdf-list', 'bibliography', 'cards')),
                        Toggle::make('group_by_year')
                            ->label('Raggruppa per anno')
                            ->helperText('Mostra l\'anno come titolo di ogni gruppo (es. BOL).')
                            ->visible($is('pdf-list')),
                    ]),
                Section::make('Contenuto della pagina')
                    ->description('Per i Quaderni: testo, PDF e donazione minima. Le pagine interne si aggiungono più sotto, dopo aver salvato.')
                    ->columnSpanFull()
                    ->columns(2)
                    ->visible($is('detail'))
                    ->schema([
                        Fields::richText('body', 'Testo'),
                        Fields::pdf('pdf_path', 'PDF scaricabile', 'pubblicazioni'),
                        TextInput::make('price')
                            ->label('Donazione minima')
                            ->placeholder('Es. € 18')
                            ->maxLength(50),
                    ]),
            ]);
    }
}
