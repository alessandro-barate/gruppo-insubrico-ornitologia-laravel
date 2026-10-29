<?php

namespace App\Providers;

use App\Models\GalleryImage;
use App\Models\LinkSubsection;
use App\Models\News;
use App\Models\OutreachItem;
use App\Models\OutreachSection;
use App\Models\Project;
use App\Models\ProjectSection;
use App\Models\PublicationItem;
use App\Models\PublicationPage;
use App\Models\PublicationSection;
use App\Services\SiteSearch;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // L'indice della ricerca resta in cache: si rigenera alla prima
        // ricerca dopo una modifica fatta dal pannello.
        $models = [
            News::class, PublicationSection::class, PublicationItem::class,
            PublicationPage::class, ProjectSection::class, Project::class,
            GalleryImage::class, OutreachSection::class, OutreachItem::class,
            LinkSubsection::class,
        ];

        foreach ($models as $model) {
            $model::saved(fn () => SiteSearch::flush());
            $model::deleted(fn () => SiteSearch::flush());
        }
    }
}
