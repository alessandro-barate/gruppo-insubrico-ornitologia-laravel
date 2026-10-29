<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use App\Models\ContactMessage;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Ricevuto')->dateTime('d/m/Y H:i')->sortable(),
                TextColumn::make('name')->label('Nome')
                    ->formatStateUsing(fn (ContactMessage $r) => "{$r->name} {$r->surname}")
                    ->searchable(['name', 'surname']),
                TextColumn::make('mail')->label('Email')->searchable()->copyable(),
                TextColumn::make('message')->label('Messaggio')->limit(80)->wrap(),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('reply')
                    ->label('Rispondi')
                    ->icon(Heroicon::OutlinedArrowUturnLeft)
                    ->url(fn (ContactMessage $r) => 'mailto:'.$r->mail.'?subject='.rawurlencode('Re: messaggio dal sito del G.I.O.')),
                DeleteAction::make(),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
