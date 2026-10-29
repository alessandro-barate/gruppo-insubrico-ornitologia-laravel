<?php

namespace App\Filament\Resources\PageContents;

use App\Filament\Resources\PageContents\Pages\EditPageContent;
use App\Filament\Resources\PageContents\Pages\ListPageContents;
use App\Filament\Resources\PageContents\Schemas\PageContentForm;
use App\Filament\Resources\PageContents\Tables\PageContentsTable;
use App\Models\PageContent;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use UnitEnum;

/**
 * Testi e immagini fissi delle pagine (titoli, introduzioni, copertine).
 * I campi si registrano nel PageContentSeeder: qui si modificano soltanto.
 * Un campo vuoto = il sito usa il testo scritto nel frontend.
 */
class PageContentResource extends Resource
{
    protected static ?string $model = PageContent::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentText;

    protected static string|UnitEnum|null $navigationGroup = 'Pagine';

    protected static ?int $navigationSort = 60;

    protected static ?string $navigationLabel = 'Testi delle pagine';

    protected static ?string $modelLabel = 'testo';

    protected static ?string $pluralModelLabel = 'Testi delle pagine';

    protected static ?string $recordTitleAttribute = 'label';

    public const PAGE_NAMES = [
        'home' => 'Home',
        'chi-siamo' => 'Chi siamo',
        'news' => 'News',
        'progetti' => 'Progetti',
        'divulgazione' => 'Divulgazione',
        'pubblicazioni' => 'Pubblicazioni',
        'links' => 'Social e link utili',
    ];

    public static function pageName(string $page): string
    {
        return self::PAGE_NAMES[$page] ?? ucfirst(str_replace('-', ' ', $page));
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function form(Schema $schema): Schema
    {
        return PageContentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PageContentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPageContents::route('/'),
            'edit' => EditPageContent::route('/{record}/edit'),
        ];
    }
}
