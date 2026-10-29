<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GalleryImage;
use App\Models\PageContent;
use App\Models\ProjectSection;
use App\Support\FileUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /** GET /api/progetti → { intro_text, gallery } */
    public function page(): JsonResponse
    {
        return response()->json([
            'intro_text' => PageContent::valueFor('progetti', 'intro', ''),
            'gallery' => GalleryImage::where('gallery', 'progetti')->ordered()->get()
                ->map(fn (GalleryImage $g) => array_filter([
                    'src' => FileUrl::to($g->image_path),
                    'alt' => $g->alt ?? '',
                    'caption' => $g->caption,
                ], fn ($v) => $v !== null))
                ->values(),
        ]);
    }

    /** GET /api/progetti/sections?only[]=atlanti → card delle sezioni */
    public function sections(Request $request): JsonResponse
    {
        $only = array_filter((array) $request->query('only', []));

        return response()->json(
            ProjectSection::ordered()
                ->when($only, fn ($q) => $q->whereIn('slug', $only))
                ->get()->map->toCard()->values()
        );
    }

    /**
     * GET /api/progetti/{section}/{project?}
     * Come ProgettiNode in useProgetti.ts:
     *   { kind: "section", section, projects, projectCards }
     *   { kind: "project", section, project }
     */
    public function show(string $section, ?string $project = null): JsonResponse
    {
        $sectionModel = ProjectSection::where('slug', $section)->first();
        abort_unless($sectionModel, 404, 'Pagina non trovata');

        if ($project !== null) {
            abort_unless($sectionModel->projects_are_navigable, 404, 'Pagina non trovata');

            $projectModel = $sectionModel->projects()->where('slug', $project)->first();
            abort_unless($projectModel, 404, 'Pagina non trovata');

            return response()->json([
                'kind' => 'project',
                'section' => $sectionModel->toApi(),
                'project' => $projectModel->setRelation('section', $sectionModel)->toApi(),
            ]);
        }

        $projects = $sectionModel->has_projects
            ? $sectionModel->projects->each->setRelation('section', $sectionModel)
            : collect();

        return response()->json([
            'kind' => 'section',
            'section' => $sectionModel->toApi(),
            'projects' => $projects->map->toApi()->values(),
            'projectCards' => $sectionModel->projects_are_navigable
                ? $projects->map->toCard()->values()
                : [],
        ]);
    }
}
