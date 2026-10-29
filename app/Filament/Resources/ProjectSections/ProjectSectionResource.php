<?php

namespace App\Filament\Resources\ProjectSections;

use App\Filament\Resources\ProjectSections\Pages\CreateProjectSection;
use App\Filament\Resources\ProjectSections\Pages\EditProjectSection;
use App\Filament\Resources\ProjectSections\Pages\ListProjectSections;
use App\Filament\Resources\ProjectSections\Schemas\ProjectSectionForm;
use App\Filament\Resources\ProjectSections\Tables\ProjectSectionsTable;
use App\Models\ProjectSection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ProjectSectionResource extends Resource
{
    protected static ?string $model = ProjectSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedFolder;

    protected static string|UnitEnum|null $navigationGroup = 'Progetti';

    protected static ?int $navigationSort = 31;

    protected static ?string $navigationLabel = 'Sezioni';

    protected static ?string $modelLabel = 'sezione';

    protected static ?string $pluralModelLabel = 'Sezioni dei progetti';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return ProjectSectionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProjectSectionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListProjectSections::route('/'),
            'create' => CreateProjectSection::route('/create'),
            'edit' => EditProjectSection::route('/{record}/edit'),
        ];
    }
}
