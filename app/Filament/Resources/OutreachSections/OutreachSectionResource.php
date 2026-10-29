<?php

namespace App\Filament\Resources\OutreachSections;

use App\Filament\Resources\OutreachSections\Pages\CreateOutreachSection;
use App\Filament\Resources\OutreachSections\Pages\EditOutreachSection;
use App\Filament\Resources\OutreachSections\Pages\ListOutreachSections;
use App\Filament\Resources\OutreachSections\RelationManagers\ItemsRelationManager;
use App\Filament\Resources\OutreachSections\Schemas\OutreachSectionForm;
use App\Filament\Resources\OutreachSections\Tables\OutreachSectionsTable;
use App\Models\OutreachSection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class OutreachSectionResource extends Resource
{
    protected static ?string $model = OutreachSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static string|UnitEnum|null $navigationGroup = 'Contenuti';

    protected static ?int $navigationSort = 40;

    protected static ?string $navigationLabel = 'Divulgazione';

    protected static ?string $modelLabel = 'sottosezione di Divulgazione';

    protected static ?string $pluralModelLabel = 'Divulgazione';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return OutreachSectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OutreachSectionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [ItemsRelationManager::class];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOutreachSections::route('/'),
            'create' => CreateOutreachSection::route('/create'),
            'edit' => EditOutreachSection::route('/{record}/edit'),
        ];
    }
}
