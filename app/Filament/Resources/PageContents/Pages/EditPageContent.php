<?php

namespace App\Filament\Resources\PageContents\Pages;

use App\Filament\Resources\PageContents\PageContentResource;
use App\Models\PageContent;
use Filament\Resources\Pages\EditRecord;

class EditPageContent extends EditRecord
{
    protected static string $resource = PageContentResource::class;

    /** HTML ripulito e testo vuoto = torna al predefinito */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $value = $data['value'] ?? null;

        if (is_string($value) && $this->record->type === 'html') {
            $value = strip_tags($value, PageContent::ALLOWED_TAGS);
        }

        $data['value'] = (is_string($value) && trim(strip_tags($value)) === '' && $this->record->type !== 'image')
            ? null
            : $value;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
