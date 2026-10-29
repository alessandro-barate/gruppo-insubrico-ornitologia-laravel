<?php

namespace App\Filament\Resources\PublicationSections\RelationManagers;

use App\Filament\Support\Fields;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/** Pagine interne di un Quaderno, mostrate a coppie "a libro aperto" */
class PagesRelationManager extends RelationManager
{
    protected static string $relationship = 'pages';

    protected static ?string $title = 'Pagine interne';

    protected static ?string $modelLabel = 'pagina';

    protected static ?string $pluralModelLabel = 'pagine';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->type === 'detail';
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Fields::image('image_path', 'Immagine della pagina', 'pubblicazioni/pagine')->required(),
            TextInput::make('alt')->label('Descrizione (per i lettori di schermo)')->maxLength(255),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->description('L\'ordine è quello di lettura: la pagina le mostra a coppie (1-2, 3-4…). Trascina per riordinare.')
            ->defaultSort('sort')
            ->reorderable('sort')
            ->columns([
                ImageColumn::make('image_path')->label('Pagina')->disk('public')->height(80),
                TextColumn::make('alt')->label('Descrizione'),
            ])
            ->headerActions([
                CreateAction::make()->label('Aggiungi pagina'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([DeleteBulkAction::make()]),
            ]);
    }
}
