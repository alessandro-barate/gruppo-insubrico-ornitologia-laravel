<?php

namespace App\Support;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Trasforma un percorso salvato nel database nell'indirizzo da dare al
 * frontend:
 *  - "https://…"           → così com'è (link esterno)
 *  - "/documents/…"        → così com'è: file ancora servito dal frontend
 *                            (dati importati dai mock, vedi gio:import-files)
 *  - "pubblicazioni/x.pdf" → URL pubblico del disco "public" di Laravel
 *                            (file caricato dal pannello)
 */
class FileUrl
{
    public static function to(?string $path): ?string
    {
        $path = trim((string) $path);

        if ($path === '') {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://', '/'])) {
            return $path;
        }

        return Storage::disk('public')->url($path);
    }
}
