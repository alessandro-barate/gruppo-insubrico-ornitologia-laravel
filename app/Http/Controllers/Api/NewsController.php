<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * GET /api/news?page=1&perPage=6
     * { data: News[], total, page, perPage } — dalla più recente.
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->query('perPage', 6), 1), 50);
        $page = max((int) $request->query('page', 1), 1);

        $query = News::published()->orderByDesc('date')->orderByDesc('id');
        $total = (clone $query)->count();

        return response()->json([
            'data' => $query->forPage($page, $perPage)->get()->map->toApi()->values(),
            'total' => $total,
            'page' => $page,
            'perPage' => $perPage,
        ]);
    }

    /** GET /api/news/{slug} */
    public function show(string $slug): JsonResponse
    {
        $news = News::published()->where('slug', $slug)->first();

        abort_unless($news, 404, 'News non trovata');

        return response()->json($news->toApi());
    }
}
