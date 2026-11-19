<?php

namespace App\Filament\Resources\PublicContributions\Tables;

use App\Support\FileUrl;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PublicContributionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('year', 'desc')
            ->columns([
                TextColumn::make('year')->label('Anno')->sortable(),
                TextColumn::make('file_path')
                    ->label('Documento')
                    ->formatStateUsing(fn (?string $state) => basename((string) $state))
                    ->url(fn ($record) => FileUrl::to($record->file_path), shouldOpenInNewTab: true)
                    ->color('primary'),
                TextColumn::make('note')->label('Nota')->placeholder('—'),
            ])
            ->emptyStateHeading('Nessun anno inserito')
            ->emptyStateDescription('Aggiungi un anno solo quando i contributi pubblici ricevuti superano i 10.000 €. Finché l\'elenco è vuoto, sul sito si vede solo il testo sull\'obbligo di legge.')
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
