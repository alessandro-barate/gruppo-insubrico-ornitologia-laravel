<?php

namespace App\Filament\Resources\Projects\Tables;

use App\Models\Project;
use App\Models\ProjectSection;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->reorderable('sort')
            ->paginated(false)
            ->defaultGroup(
                Group::make('project_section_id')
                    ->label('Sezione')
                    ->getTitleFromRecordUsing(fn (Project $r) => $r->section?->title)
            )
            ->columns([
                TextColumn::make('title')->label('Titolo')->searchable()->wrap(),
                TextColumn::make('blocks')
                    ->label('Documenti')
                    ->state(fn (Project $r) => collect($r->blocks ?? [])->sum(fn ($b) => count($b['links'] ?? []))),
            ])
            ->filters([
                SelectFilter::make('project_section_id')
                    ->label('Sezione')
                    ->options(fn () => ProjectSection::ordered()->pluck('title', 'id')),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
