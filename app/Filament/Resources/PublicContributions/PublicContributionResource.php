<?php

namespace App\Filament\Resources\PublicContributions;

use App\Filament\Resources\PublicContributions\Pages\CreatePublicContribution;
use App\Filament\Resources\PublicContributions\Pages\EditPublicContribution;
use App\Filament\Resources\PublicContributions\Pages\ListPublicContributions;
use App\Filament\Resources\PublicContributions\Schemas\PublicContributionForm;
use App\Filament\Resources\PublicContributions\Tables\PublicContributionsTable;
use App\Models\PublicContribution;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

/**
 * Trasparenza sui contributi pubblici (Legge 124/2017): gli anni con il
 * PDF dei contributi ricevuti, mostrati nella finestra «Trasparenza sui
 * Contributi Pubblici» del footer.
 */
class PublicContributionResource extends Resource
{
    protected static ?string $model = PublicContribution::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedScale;

    protected static string|UnitEnum|null $navigationGroup = 'Pagine';

    protected static ?int $navigationSort = 65;

    protected static ?string $navigationLabel = 'Trasparenza contributi';

    protected static ?string $modelLabel = 'anno';

    protected static ?string $pluralModelLabel = 'Trasparenza sui contributi pubblici';

    // Titoli come scritti qui, senza Maiuscole A Ogni Parola
    protected static bool $hasTitleCaseModelLabel = false;

    protected static ?string $recordTitleAttribute = 'year';

    public static function form(Schema $schema): Schema
    {
        return PublicContributionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PublicContributionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPublicContributions::route('/'),
            'create' => CreatePublicContribution::route('/create'),
            'edit' => EditPublicContribution::route('/{record}/edit'),
        ];
    }
}
