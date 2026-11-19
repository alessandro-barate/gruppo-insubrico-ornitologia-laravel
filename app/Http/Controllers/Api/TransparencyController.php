<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PublicContribution;
use Illuminate\Http\JsonResponse;

class TransparencyController extends Controller
{
    /** GET /api/trasparenza → [{ year, note, file_url }], dall'anno più recente */
    public function index(): JsonResponse
    {
        return response()->json(
            PublicContribution::orderByDesc('year')->get()->map->toApi()->values()
        );
    }
}
