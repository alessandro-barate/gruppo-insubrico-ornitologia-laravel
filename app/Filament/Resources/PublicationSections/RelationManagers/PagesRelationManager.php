<?php

namespace App\Filament\Resources\PublicationSections\RelationManagers;

use App\Filament\Support\Fields;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
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
                $this->uploadManyAction(),
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

    /**
     * Carica più pagine in una volta: vengono aggiunte in fondo, nell'ordine
     * in cui compaiono nel riquadro (si possono trascinare prima di salvare
     * e riordinare anche dopo, nella tabella).
     */
    protected function uploadManyAction(): Action
    {
        return Action::make('uploadMany')
            ->label('Carica più pagine')
            ->icon('heroicon-o-arrow-up-tray')
            ->color('gray')
            ->modalHeading('Carica più pagine')
            ->modalDescription('Seleziona o trascina tutte le immagini insieme. Controlla l\'ordine nel riquadro (si trascinano per spostarle): le pagine vengono aggiunte in fondo, in quell\'ordine.')
            ->modalSubmitActionLabel('Aggiungi le pagine')
            ->schema([
                FileUpload::make('images')
                    ->label('Pagine')
                    ->multiple()
                    ->image()
                    ->disk('public')
                    ->directory('pubblicazioni/pagine')
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(Fields::MAX_IMAGE_KB)
                    ->maxFiles(100)
                    ->reorderable()
                    ->appendFiles()
                    ->panelLayout('grid')
                    ->helperText('JPG, PNG o WebP, al massimo 8 MB l\'una. Meglio WebP, più leggero.')
                    ->required(),
            ])
            ->action(function (array $data): void {
                $paths = array_values($data['images'] ?? []);

                foreach ($paths as $path) {
                    // "sort" in fondo alla lista lo assegna Sortable
                    $this->getOwnerRecord()->pages()->create(['image_path' => $path]);
                }

                Notification::make()
                    ->title(count($paths) === 1 ? '1 pagina aggiunta' : count($paths).' pagine aggiunte')
                    ->success()
                    ->send();
            });
    }
}
