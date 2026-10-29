<?php

namespace App\Filament\Resources\PublicationSections\Tables;

use App\Models\PublicationSection;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PublicationSectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->with('parent')->withCount(['items', 'children']))
            ->defaultSort('sort')
            ->reorderable('sort')
            ->paginated(false)
            // Raggruppate per sezione "madre"; dentro ogni gruppo, l'ordine del sito
            ->defaultGroup(
                Group::make('parent_id')
                    ->label('Dentro')
                    ->collapsible()
                    ->getTitleFromRecordUsing(fn (PublicationSection $r) => $r->parent?->fullTitle() ?? 'Pagina Pubblicazioni (card principali)')
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Titolo')
                    ->searchable()
                    ->description(fn (PublicationSection $r) => $r->path()),
                TextColumn::make('type')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => PublicationSection::TYPES[$state] ?? $state),
                TextColumn::make('items_count')
                    ->label('Contenuto')
                    ->state(fn (PublicationSection $r) => match ($r->type) {
                        'group' => $r->children_count.' sezioni',
                        'detail' => '—',
                        default => $r->items_count.' voci',
                    }),
            ])
            ->filters([
                SelectFilter::make('type')->label('Tipo')->options(PublicationSection::TYPES),
            ])
            ->recordActions([
                EditAction::make(),
            ]);
    }
}
