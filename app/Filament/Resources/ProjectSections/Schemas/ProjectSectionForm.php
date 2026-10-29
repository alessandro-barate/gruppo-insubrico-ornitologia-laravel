<?php

namespace App\Filament\Resources\ProjectSections\Schemas;

use App\Filament\Support\Fields;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProjectSectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([
                Section::make('Sezione')
                    ->columnSpan(2)
                    ->columns(2)
                    ->schema([
                        Fields::title()->columnSpanFull(),
                        Fields::slug()->unique(ignoreRecord: true),
                        TextInput::make('intro_excerpt')
                            ->label('Testo breve della card')
                            ->maxLength(255),
                        Fields::richText('intro_text', 'Testo introduttivo'),
                    ]),
                Section::make('Aspetto')
                    ->columnSpan(1)
                    ->schema([
                        Fields::image('image_path', 'Immagine della card', 'progetti'),
                        Toggle::make('has_projects')
                            ->label('Contiene progetti')
                            ->default(true),
                        Toggle::make('projects_are_navigable')
                            ->label('Ogni progetto ha una sua pagina')
                            ->helperText('Spento: i progetti si leggono uno sotto l\'altro nella pagina della sezione.'),
                    ]),
            ]);
    }
}
