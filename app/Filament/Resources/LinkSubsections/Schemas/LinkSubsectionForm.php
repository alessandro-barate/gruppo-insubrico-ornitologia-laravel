<?php

namespace App\Filament\Resources\LinkSubsections\Schemas;

use App\Models\LinkSubsection;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class LinkSubsectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('category')
                    ->label('Pannello')
                    ->options(LinkSubsection::CATEGORIES)
                    ->required(),
                TextInput::make('title')
                    ->label('Titolo della colonna')
                    ->required()
                    ->maxLength(255),
                Repeater::make('groups')
                    ->label('Gruppi di link')
                    ->addActionLabel('Aggiungi un gruppo')
                    ->defaultItems(1)
                    ->reorderable()
                    ->collapsible()
                    ->itemLabel(fn (array $state): string => ($state['title'] ?? null) ?: 'Gruppo senza titolo')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('title')
                            ->label('Titolo del gruppo (facoltativo)')
                            ->maxLength(255),
                        Repeater::make('links')
                            ->label('Link')
                            ->addActionLabel('Aggiungi un link')
                            ->reorderable()
                            ->columns(3)
                            ->itemLabel(fn (array $state): ?string => $state['title'] ?? null)
                            ->schema([
                                TextInput::make('title')->label('Nome')->required(),
                                TextInput::make('href')->label('Indirizzo')->url()->required(),
                                TextInput::make('subtitle')->label('Descrizione (facoltativa)'),
                            ]),
                    ]),
            ]);
    }
}
