<?php

namespace App\Filament\Resources\OutreachSections\RelationManagers;

use App\Filament\Support\Fields;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

/** Corsi, eventi, convegni: locandina, anno, testo, link */
class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Voci';

    protected static ?string $modelLabel = 'voce';

    protected static ?string $pluralModelLabel = 'voci';

    public function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('title')->label('Titolo')->required()->maxLength(255),
            TextInput::make('year')->label('Anno')->maxLength(10),
            Fields::image('image_path', 'Locandina', 'divulgazione')->columnSpanFull(),
            Fields::richText('body', 'Testo (facoltativo)'),
            Repeater::make('links')
                ->label('Link e documenti (facoltativi)')
                ->addActionLabel('Aggiungi un link')
                ->defaultItems(0)
                ->columns(2)
                ->columnSpanFull()
                ->schema([
                    TextInput::make('label')->label('Testo del link')->required(),
                    TextInput::make('url')->label('Indirizzo di un sito')->url()
                        ->helperText('Oppure carica un file qui sotto.'),
                    FileUpload::make('file')->label('File')->disk('public')->directory('divulgazione')
                        ->preserveFilenames()->acceptedFileTypes(Fields::DOCUMENT_TYPES)
                        ->maxSize(Fields::MAX_PDF_KB)->openable()->columnSpanFull(),
                ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->reorderable('sort')
            ->paginated([25, 50, 100])
            ->columns([
                ImageColumn::make('image_path')->label('Locandina')->disk('public')->height(56),
                TextColumn::make('year')->label('Anno')->sortable(),
                TextColumn::make('title')->label('Titolo')->searchable()->wrap(),
            ])
            ->headerActions([CreateAction::make()->label('Aggiungi voce')])
            ->recordActions([EditAction::make(), DeleteAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
