<?php

namespace App\Filament\Resources\PublicationSections\RelationManagers;

use App\Filament\Support\Fields;
use App\Models\PublicationItem;
use App\Models\PublicationSection;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

/** Voci di una sezione-lista: PDF (BOL, Resoconti), citazioni, schede */
class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $title = 'Voci';

    protected static ?string $modelLabel = 'voce';

    protected static ?string $pluralModelLabel = 'voci';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return in_array($ownerRecord->type, ['pdf-list', 'bibliography', 'cards'], true);
    }

    private function type(): string
    {
        /** @var PublicationSection $owner */
        $owner = $this->getOwnerRecord();

        return $owner->type;
    }

    public function form(Schema $schema): Schema
    {
        return $schema->columns(2)->components(match ($this->type()) {
            'pdf-list' => [
                TextInput::make('title')->label('Titolo')->required()->maxLength(500),
                TextInput::make('year')->label('Anno')->numeric()->minValue(1900)->maxValue(2100)
                    ->helperText('Serve per l\'ordine (più recente prima) e per i gruppi per anno.'),
                Fields::pdf('file_path', 'PDF', 'pubblicazioni')->required()->columnSpanFull(),
            ],
            'bibliography' => [
                Textarea::make('text')
                    ->label('Citazione')
                    ->helperText('Autori, anno, titolo, rivista. Puoi usare <em>…</em> per il corsivo.')
                    ->rows(4)->required()->columnSpanFull(),
                Fields::pdf('file_path', 'PDF (facoltativo)', 'pubblicazioni/bibliografia')
                    ->helperText('Se c\'è, la citazione diventa un link che apre il PDF.')
                    ->columnSpanFull(),
            ],
            default => [ // cards
                TextInput::make('title')->label('Titolo')->required()->maxLength(500)->columnSpanFull(),
                Fields::richText('text', 'Testo'),
                Fields::image('image_path', 'Immagine', 'pubblicazioni')->columnSpanFull(),
            ],
        });
    }

    public function table(Table $table): Table
    {
        $type = $this->type();

        return $table
            // pdf-list: il sito le ordina per anno (più recente prima);
            // bibliografia e schede: l'ordine è quello della lista
            ->defaultSort($type === 'pdf-list' ? 'year' : 'sort', $type === 'pdf-list' ? 'desc' : 'asc')
            ->reorderable($type === 'pdf-list' ? null : 'sort')
            ->description($type === 'bibliography'
                ? 'Le citazioni compaiono sul sito in quest\'ordine (per autore, poi per anno). Una nuova va in fondo: trascinala al suo posto con il pulsante di riordino.'
                : null)
            ->paginated([25, 50, 100])
            ->columns(match ($type) {
                'pdf-list' => [
                    TextColumn::make('year')->label('Anno')->sortable(),
                    TextColumn::make('title')->label('Titolo')->searchable(),
                    IconColumn::make('file_path')->label('PDF')->boolean(),
                ],
                'bibliography' => [
                    TextColumn::make('text')->label('Citazione')->html()->wrap()->searchable()->limit(220),
                    IconColumn::make('file_path')->label('PDF')->boolean()
                        ->getStateUsing(fn (PublicationItem $r) => filled($r->file_path)),
                ],
                default => [
                    TextColumn::make('title')->label('Titolo')->searchable(),
                ],
            })
            ->headerActions([
                CreateAction::make()->label('Aggiungi voce'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
