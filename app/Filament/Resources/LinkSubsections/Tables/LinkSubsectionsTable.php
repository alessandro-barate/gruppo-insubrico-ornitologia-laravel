<?php

namespace App\Filament\Resources\LinkSubsections\Tables;

use App\Models\LinkSubsection;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class LinkSubsectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->reorderable('sort')
            ->paginated(false)
            ->defaultGroup(
                Group::make('category')
                    ->label('Pannello')
                    ->getTitleFromRecordUsing(fn (LinkSubsection $r) => LinkSubsection::CATEGORIES[$r->category] ?? $r->category)
            )
            ->columns([
                TextColumn::make('title')->label('Colonna')->searchable(),
                TextColumn::make('groups')
                    ->label('Link')
                    ->state(fn (LinkSubsection $r) => collect($r->groups ?? [])->sum(fn ($g) => count($g['links'] ?? []))),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
