<?php

namespace App\Filament\Resources\News\Tables;

use App\Models\News;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class NewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                TextColumn::make('date')
                    ->label('Data')
                    ->date('d/m/Y')
                    ->sortable(),
                TextColumn::make('title')
                    ->label('Titolo')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('category')
                    ->label('Categoria')
                    ->badge()
                    ->searchable(),
                IconColumn::make('is_published')
                    ->label('Visibile')
                    ->boolean(),
                // In Home adesso (tiene conto anche della data «In Home fino al»)
                IconColumn::make('on_home')
                    ->label('In Home')
                    ->state(fn (News $record) => $record->isOnHome())
                    ->boolean()
                    ->tooltip(fn (News $record) => $record->home_until
                        ? 'In Home fino al '.$record->home_until->format('d/m/Y')
                        : null),
            ])
            ->filters([
                TernaryFilter::make('is_published')->label('Visibile sul sito'),
                TernaryFilter::make('on_home')
                    ->label('In Home')
                    ->trueLabel('Solo quelle in Home')
                    ->falseLabel('Solo quelle fuori dalla Home')
                    ->queries(
                        true: fn (Builder $query) => $query->onHome(),
                        false: fn (Builder $query) => $query->where(fn (Builder $q) => $q
                            ->where('is_published', false)
                            ->orWhere('show_on_home', false)
                            ->orWhereDate('home_until', '<', today())),
                    ),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
