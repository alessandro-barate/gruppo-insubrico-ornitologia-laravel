<?php

namespace App\Filament\Support;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Support\Str;

/**
 * Campi riusati da più schermate del pannello, sempre con le stesse
 * etichette e gli stessi limiti.
 */
class Fields
{
    // Limiti di caricamento (in KB). Il PDF più grande oggi è ~22 MB.
    public const MAX_PDF_KB = 51200;   // 50 MB
    public const MAX_IMAGE_KB = 8192;  // 8 MB

    public const DOCUMENT_TYPES = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'application/vnd.ms-powerpoint',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation',
    ];

    // Stesse icone di ProgettiTypedLink / news nel frontend
    public const LINK_KINDS = [
        'pdf' => 'PDF',
        'word' => 'Word',
        'excel' => 'Excel',
        'powerpoint' => 'PowerPoint',
        'external' => 'Link a un sito esterno',
    ];

    /** Titolo che, alla creazione, compila da solo lo slug */
    public static function title(string $name = 'title', string $label = 'Titolo'): TextInput
    {
        return TextInput::make($name)
            ->label($label)
            ->required()
            ->maxLength(255)
            ->live(onBlur: true)
            ->afterStateUpdated(function (string $operation, ?string $state, Set $set, Get $get) {
                if ($operation === 'create' && blank($get('slug'))) {
                    $set('slug', Str::slug((string) $state));
                }
            });
    }

    public static function slug(): TextInput
    {
        return TextInput::make('slug')
            ->label('Indirizzo (slug)')
            ->helperText('Parte finale dell\'indirizzo della pagina: solo minuscole, numeri e trattini. Cambiandolo, i vecchi link smettono di funzionare.')
            ->required()
            ->maxLength(255)
            ->regex('/^[a-z0-9]+(?:-[a-z0-9]+)*$/')
            ->validationMessages(['regex' => 'Usa solo lettere minuscole, numeri e trattini.']);
    }

    public static function richText(string $name, string $label): RichEditor
    {
        return RichEditor::make($name)
            ->label($label)
            ->toolbarButtons([
                ['bold', 'italic', 'underline', 'link'],
                ['h2', 'h3'],
                ['bulletList', 'orderedList'],
                ['undo', 'redo'],
            ])
            ->columnSpanFull();
    }

    public static function image(string $name, string $label, string $directory): FileUpload
    {
        return FileUpload::make($name)
            ->label($label)
            ->image()
            ->disk('public')
            ->directory($directory)
            ->maxSize(self::MAX_IMAGE_KB)
            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->helperText('JPG, PNG o WebP, al massimo 8 MB. Meglio WebP, più leggero.')
            ->openable();
    }

    public static function pdf(string $name, string $label, string $directory): FileUpload
    {
        return FileUpload::make($name)
            ->label($label)
            ->disk('public')
            ->directory($directory)
            ->preserveFilenames()
            ->acceptedFileTypes(['application/pdf'])
            ->maxSize(self::MAX_PDF_KB)
            ->helperText('PDF, al massimo 50 MB.')
            ->openable()
            ->downloadable();
    }

    /**
     * Elenco di file o link con tipo (PDF, Word, Excel, PowerPoint, sito
     * esterno). Per i file si carica il documento, per i siti l'indirizzo.
     * Salva [{ kind|type, label, file?, url? }].
     */
    public static function typedLinks(string $name, string $label, string $directory, string $kindKey = 'kind'): Repeater
    {
        return Repeater::make($name)
            ->label($label)
            ->addActionLabel('Aggiungi un file o un link')
            ->defaultItems(0)
            ->reorderable()
            ->collapsible()
            ->collapsed() // si apre il singolo file per modificarlo
            ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
            ->columns(2)
            ->schema([
                Select::make($kindKey)
                    ->label('Tipo')
                    ->options(self::LINK_KINDS)
                    ->default('pdf')
                    ->required()
                    ->live(),
                TextInput::make('label')
                    ->label('Testo del link')
                    ->required()
                    ->maxLength(500),
                FileUpload::make('file')
                    ->label('File')
                    ->disk('public')
                    ->directory($directory)
                    ->preserveFilenames()
                    ->acceptedFileTypes(self::DOCUMENT_TYPES)
                    ->maxSize(self::MAX_PDF_KB)
                    ->openable()
                    ->downloadable()
                    ->required(fn (Get $get) => $get($kindKey) !== 'external')
                    ->visible(fn (Get $get) => $get($kindKey) !== 'external')
                    ->columnSpanFull(),
                TextInput::make('url')
                    ->label('Indirizzo del sito')
                    ->url()
                    ->placeholder('https://…')
                    ->required(fn (Get $get) => $get($kindKey) === 'external')
                    ->visible(fn (Get $get) => $get($kindKey) === 'external')
                    ->columnSpanFull(),
            ]);
    }
}
