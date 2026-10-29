<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PageContent;
use App\Models\PublicationSection;
use Illuminate\Http\JsonResponse;

class PublicationController extends Controller
{
    /** GET /api/pubblicazioni → { intro_text, cards } */
    public function home(): JsonResponse
    {
        return response()->json([
            'intro_text' => PageContent::valueFor('pubblicazioni', 'intro', ''),
            'cards' => PublicationSection::whereNull('parent_id')->ordered()->get()
                ->map->toCard()->values(),
        ]);
    }

    /**
     * GET /api/pubblicazioni/{path} — es. "uccelli-varese/resoconti-ornitologici"
     * → { node, childCards, crumbs }. crumbs = le voci della breadcrumb dopo
     * "Pubblicazioni" (l'ultima è la pagina stessa).
     */
    public function show(string $path): JsonResponse
    {
        $node = null;
        $crumbs = [];
        $url = '/pubblicazioni';

        foreach (array_filter(explode('/', $path)) as $slug) {
            $node = PublicationSection::where('parent_id', $node?->id)
                ->where('slug', $slug)->first();

            abort_unless($node, 404, 'Pagina non trovata');

            $url .= '/'.$slug;
            $crumbs[] = ['title' => $node->title, 'to' => $url];
        }

        abort_unless($node, 404, 'Pagina non trovata');

        $node->load(['children', 'items', 'pages']);

        return response()->json([
            'node' => $node->toNode(),
            'childCards' => $node->type === 'group'
                ? $node->children->map->toCard()->values()
                : [],
            'crumbs' => $crumbs,
        ]);
    }
}
