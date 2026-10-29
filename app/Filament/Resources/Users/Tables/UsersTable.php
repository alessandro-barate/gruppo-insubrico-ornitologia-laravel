<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Nome')->searchable(),
                TextColumn::make('email')->label('Email')->searchable(),
                TextColumn::make('created_at')->label('Creato il')->date('d/m/Y'),
            ])
            ->recordActions([
                EditAction::make(),
                // Nessuno può cancellare il proprio account (resterebbe chiuso fuori)
                DeleteAction::make()->hidden(fn (User $r) => $r->is(auth()->user())),
            ]);
    }
}
