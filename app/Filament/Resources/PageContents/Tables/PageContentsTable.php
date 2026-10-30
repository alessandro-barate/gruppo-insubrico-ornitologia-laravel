<?php

namespace App\Filament\Resources\PageContents\Tables;

use App\Filament\Resources\PageContents\PageContentResource;
use App\Models\PageContent;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PageContentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort')
            ->defaultGroup(
                Group::make('page')
                    ->label('Pagina')
                    ->getTitleFromRecordUsing(fn (PageContent $r) => PageContentResource::pageName($r->page))
                    // Gruppi nell'ordine del sito, non in ordine alfabetico
                    ->orderQueryUsing(fn (Builder $query, string $direction) => $query->orderByRaw(self::pageOrderSql()))
                    ->collapsible()
            )
            ->filters([
                SelectFilter::make('page')
                    ->label('Pagina')
                    ->options(PageContentResource::PAGE_NAMES),
            ])
            ->searchable(['label'])
            ->paginated(false)
            ->columns([
                TextColumn::make('label')->label('Campo')->wrap(),
                // Anteprima delle immagini caricate dal pannello
                ImageColumn::make('preview')
                    ->label('')
                    ->state(fn (PageContent $r) => $r->type === 'image' ? $r->public_value : null)
                    ->imageHeight(40),
                TextColumn::make('value')
                    ->label('Valore')
                    ->state(fn (PageContent $r) => match (true) {
                        blank($r->value) => 'Predefinito del sito',
                        $r->type === 'image' => 'Immagine caricata dal pannello',
                        default => Str::limit(trim(html_entity_decode(strip_tags($r->value), ENT_QUOTES | ENT_HTML5, 'UTF-8')), 90),
                    })
                    ->color(fn (PageContent $r) => blank($r->value) ? 'gray' : null)
                    ->wrap(),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('reset')
                    ->label('Ripristina')
                    ->icon(Heroicon::OutlinedArrowUturnLeft)
                    ->color('gray')
                    ->requiresConfirmation()
                    ->modalHeading('Tornare al testo predefinito?')
                    ->modalDescription('Il sito userà di nuovo il testo scritto nel frontend.')
                    ->visible(fn (PageContent $r) => filled($r->value))
                    ->action(function (PageContent $r) {
                        if ($r->type === 'image' && ! Str::startsWith($r->value, '/')) {
                            Storage::disk('public')->delete($r->value);
                        }
                        $r->update(['value' => null]);
                    }),
            ]);
    }

    /** ORDER BY che segue l'ordine di PageContentResource::PAGE_NAMES. */
    private static function pageOrderSql(): string
    {
        $cases = collect(array_keys(PageContentResource::PAGE_NAMES))
            ->map(fn (string $page, int $i) => "WHEN '".addslashes($page)."' THEN {$i}")
            ->implode(' ');

        return "CASE page {$cases} ELSE 999 END";
    }
}
