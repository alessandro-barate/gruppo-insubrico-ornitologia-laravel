<?php

namespace App\Filament\Resources\LinkSubsections;

use App\Filament\Resources\LinkSubsections\Pages\CreateLinkSubsection;
use App\Filament\Resources\LinkSubsections\Pages\EditLinkSubsection;
use App\Filament\Resources\LinkSubsections\Pages\ListLinkSubsections;
use App\Filament\Resources\LinkSubsections\Schemas\LinkSubsectionForm;
use App\Filament\Resources\LinkSubsections\Tables\LinkSubsectionsTable;
use App\Models\LinkSubsection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class LinkSubsectionResource extends Resource
{
    protected static ?string $model = LinkSubsection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedLink;

    protected static string|UnitEnum|null $navigationGroup = 'Contenuti';

    protected static ?int $navigationSort = 50;

    protected static ?string $navigationLabel = 'Link utili';

    protected static ?string $modelLabel = 'colonna di link';

    protected static ?string $pluralModelLabel = 'Link utili';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return LinkSubsectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return LinkSubsectionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLinkSubsections::route('/'),
            'create' => CreateLinkSubsection::route('/create'),
            'edit' => EditLinkSubsection::route('/{record}/edit'),
        ];
    }
}
