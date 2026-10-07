<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PageContentController extends Controller
{
    /**
     * GET /api/admin/page-contents
     * Tutti i campi, raggruppati per pagina, per costruire la schermata del pannello.
     */
    public function index(): JsonResponse
    {
        $grouped = PageContent::orderBy('page')
            ->orderBy('sort')
            ->get()
            ->groupBy('page');

        return response()->json($grouped);
    }

    /**
     * POST /api/admin/page-contents/{pageContent}
     * - campi text/html: invia "value"
     * - campi image: invia il file "image" (multipart/form-data)
     * (POST e non PUT: PHP non legge i file nelle richieste PUT multipart)
     */
    public function update(Request $request, PageContent $pageContent): JsonResponse
    {
        if ($pageContent->type === 'image') {
            $request->validate([
                'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'], // 5 MB
            ]);

            $this->deleteStoredImage($pageContent);

            $pageContent->value = $request->file('image')->store('page-contents', 'public');
        } else {
            $data = $request->validate([
                'value' => ['nullable', 'string', 'max:20000'],
            ]);

            $value = $data['value'] ?? null;

            if ($value !== null && $pageContent->type === 'html') {
                $value = strip_tags($value, PageContent::ALLOWED_TAGS);
            }

            // Stringa vuota = torna al testo di default del sito
            $pageContent->value = ($value === null || trim($value) === '') ? null : $value;
        }

        $pageContent->save();

        return response()->json($pageContent->fresh());
    }

    /**
     * DELETE /api/admin/page-contents/{pageContent}/value
     * Ripristina il default del sito (per testi e immagini).
     */
    public function reset(PageContent $pageContent): JsonResponse
    {
        $this->deleteStoredImage($pageContent);

        $pageContent->value = null;
        $pageContent->save();

        return response()->json($pageContent->fresh());
    }

    private function deleteStoredImage(PageContent $pageContent): void
    {
        if ($pageContent->type === 'image' && $pageContent->value) {
            Storage::disk('public')->delete($pageContent->value);
        }
    }
}
