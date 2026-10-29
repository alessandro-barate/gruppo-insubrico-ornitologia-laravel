<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LinkSubsection;
use Illuminate\Http\JsonResponse;

class LinkController extends Controller
{
    /** GET /api/links → { portali: [...], organizzazioni: [...], reti: [...], turismo: [...] } */
    public function index(): JsonResponse
    {
        $byCategory = LinkSubsection::ordered()->get()->groupBy('category');

        $out = [];
        foreach (array_keys(LinkSubsection::CATEGORIES) as $category) {
            $out[$category] = ($byCategory[$category] ?? collect())->map->toApi()->values();
        }

        return response()->json($out);
    }
}
