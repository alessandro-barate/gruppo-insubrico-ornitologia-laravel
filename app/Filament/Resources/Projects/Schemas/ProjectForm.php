<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Filament\Support\Fields;
use App\Models\ProjectSection;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Validation\Rules\Unique;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Progetto')
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        Fields::title()->columnSpanFull(),
                        Select::make('project_section_id')
                            ->label('Sezione')
                            ->helperText('Per spostare un progetto (es. da "in corso" a "terminati") basta cambiare la sezione.')
                            ->options(fn () => ProjectSection::ordered()->pluck('title', 'id'))
                            ->required()
                            ->live(),
                        Fields::slug()->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule, Get $get) => $rule->where('project_section_id', $get('project_section_id')),
                        ),
                        Fields::richText('intro', 'Descrizione'),
                    ]),
                Section::make('Immagine')
                    ->columnSpan(1)
                    ->schema([
                        Fields::image('image_path', 'Immagine della card', 'progetti'),
                    ]),
                Section::make('Altri blocchi di testo e documenti')
                    ->description('Nelle sezioni "in corso" e "terminati" la pagina Progetti mostra solo i blocchi senza documenti: i PDF delle ricerche vanno nella bibliografia di Altre Pubblicazioni.')
                    ->columnSpanFull()
                    ->schema([
                        Repeater::make('blocks')
                            ->hiddenLabel()
                            ->addActionLabel('Aggiungi un blocco')
                            ->defaultItems(0)
                            ->reorderable()
                            ->collapsible()
                            ->itemLabel(fn (array $state): string => filled($state['text'] ?? null)
                                ? str(strip_tags($state['text']))->limit(60)->toString()
                                : 'Blocco di documenti')
                            ->schema([
                                Fields::richText('text', 'Testo (facoltativo)'),
                                Fields::typedLinks('links', 'Documenti e link', 'progetti'),
                            ]),
                    ]),
            ]);
    }
}
