<?php

namespace App\Filament\Resources\PublicationSections;

use App\Filament\Resources\PublicationSections\Pages\CreatePublicationSection;
use App\Filament\Resources\PublicationSections\Pages\EditPublicationSection;
use App\Filament\Resources\PublicationSections\Pages\ListPublicationSections;
use App\Filament\Resources\PublicationSections\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\PublicationSections\RelationManagers\PagesRelationManager;
use App\Filament\Resources\PublicationSections\Schemas\PublicationSectionForm;
use App\Filament\Resources\PublicationSections\Tables\PublicationSectionsTable;
use App\Models\PublicationSection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class PublicationSectionResource extends Resource
{
    protected static ?string $model = PublicationSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static string|UnitEnum|null $navigationGroup = 'Contenuti';

    protected static ?int $navigationSort = 20;

    protected static ?string $navigationLabel = 'Pubblicazioni';

    protected static ?string $modelLabel = 'sezione di Pubblicazioni';

    protected static ?string $pluralModelLabel = 'Pubblicazioni';

    // Titoli come scritti qui, senza Maiuscole A Ogni Parola
    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return PublicationSectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PublicationSectionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            ItemsRelationManager::class,
            PagesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPublicationSections::route('/'),
            'create' => CreatePublicationSection::route('/create'),
            'edit' => EditPublicationSection::route('/{record}/edit'),
        ];
    }
}
