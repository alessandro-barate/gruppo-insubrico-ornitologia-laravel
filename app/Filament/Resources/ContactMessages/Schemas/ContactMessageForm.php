<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->columns(2)->components([
            TextInput::make('name')->label('Nome'),
            TextInput::make('surname')->label('Cognome'),
            TextInput::make('mail')->label('Email')->columnSpanFull(),
            Textarea::make('message')->label('Messaggio')->rows(8)->columnSpanFull(),
        ]);
    }
}
