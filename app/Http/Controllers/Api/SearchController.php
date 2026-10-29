<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\SiteSearch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    private const MIN_LENGTH = 2;
    private const MAX_LIMIT = 200;

    /** GET /api/search?q=rondone&limit=8 → { q, total, data } */
    public function __invoke(Request $request, SiteSearch $search): JsonResponse
    {
        $q = mb_substr(trim((string) $request->query('q', '')), 0, 100);
        $limit = min(max((int) $request->query('limit', 8), 1), self::MAX_LIMIT);

        if (mb_strlen($q) < self::MIN_LENGTH) {
            return response()->json(['q' => $q, 'total' => 0, 'data' => []]);
        }

        return response()->json(['q' => $q] + $search->search($q, $limit));
    }
}
