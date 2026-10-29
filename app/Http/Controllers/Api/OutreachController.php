<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\OutreachSection;
use App\Models\PageContent;
use Illuminate\Http\JsonResponse;

/** Divulgazione */
class OutreachController extends Controller
{
    /** GET /api/divulgazione → { intro_text } */
    public function page(): JsonResponse
    {
        return response()->json([
            'intro_text' => PageContent::valueFor('divulgazione', 'intro', ''),
        ]);
    }

    /** GET /api/divulgazione/subsections → card delle sottosezioni */
    public function subsections(): JsonResponse
    {
        return response()->json(OutreachSection::ordered()->get()->map->toCard()->values());
    }

    /** GET /api/divulgazione/{slug} → sottosezione con i suoi item */
    public function show(string $slug): JsonResponse
    {
        $section = OutreachSection::with('items')->where('slug', $slug)->first();
        abort_unless($section, 404, 'Pagina non trovata');

        return response()->json($section->toApi());
    }
}
