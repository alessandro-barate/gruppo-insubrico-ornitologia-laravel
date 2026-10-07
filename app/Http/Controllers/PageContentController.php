<?php

namespace App\Http\Controllers;

use App\Models\PageContent;
use Illuminate\Http\JsonResponse;

class PageContentController extends Controller
{
    /**
     * GET /api/pages/{page}
     * Restituisce una mappa chiave → valore, es.
     * { "title": "News", "intro": "<p>...</p>", "hero_image": "http://.../x.webp" }
     */
    public function show(string $page): JsonResponse
    {
        $contents = PageContent::where('page', $page)
            ->get()
            ->mapWithKeys(fn (PageContent $c) => [$c->key => $c->public_value]);

        return response()->json($contents);
    }
}
