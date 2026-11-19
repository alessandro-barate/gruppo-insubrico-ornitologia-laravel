<?php

namespace App\Filament\Resources\PublicContributions\Schemas;

use App\Filament\Support\Fields;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PublicContributionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('year')
                ->label('Anno')
                ->required()
                ->integer()
                ->minValue(2000)
                ->maxValue(2100)
                ->default(fn () => (int) now()->subYear()->format('Y'))
                ->unique(ignoreRecord: true)
                ->validationMessages(['unique' => 'C\'è già un documento per questo anno: aprilo e sostituisci il PDF.'])
                ->helperText('L\'anno a cui si riferiscono i contributi (es. 2026 per quelli ricevuti nel 2026).'),
            Fields::pdf('file_path', 'Documento', 'trasparenza')
                ->required()
                ->columnSpanFull(),
            TextInput::make('note')
                ->label('Nota (facoltativa)')
                ->maxLength(255)
                ->columnSpanFull()
                ->helperText('Compare sul sito accanto all\'anno, es. «Contributi ricevuti da enti pubblici». Lasciala vuota se non serve.'),
        ]);
    }
}
