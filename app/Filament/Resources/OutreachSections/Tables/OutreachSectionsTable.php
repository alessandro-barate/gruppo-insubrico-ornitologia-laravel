<?php

namespace App\Filament\Resources\OutreachSections\Tables;

use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OutreachSectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                TextColumn::make('title')->label('Titolo')->searchable(),
                TextColumn::make('items_count')->label('Voci')->counts('items'),
            ])
            ->recordActions([EditAction::make()]);
    }
}
