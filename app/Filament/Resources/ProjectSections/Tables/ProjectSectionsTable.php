<?php

namespace App\Filament\Resources\ProjectSections\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectSectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                TextColumn::make('title')->label('Titolo')->searchable(),
                TextColumn::make('projects_count')->label('Progetti')->counts('projects'),
                IconColumn::make('projects_are_navigable')->label('Pagine singole')->boolean(),
            ])
            ->recordActions([EditAction::make()]);
    }
}
