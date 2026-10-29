<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_panel_requires_login(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/')->assertRedirect('/admin');
    }

    public function test_every_screen_opens(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->actingAs(User::factory()->create());

        $screens = [
            '/admin', '/admin/news', '/admin/news/create', '/admin/news/1/edit',
            '/admin/publication-sections', '/admin/publication-sections/create',
            '/admin/projects', '/admin/projects/create', '/admin/projects/1/edit',
            '/admin/project-sections', '/admin/gallery-images',
            '/admin/outreach-sections', '/admin/outreach-sections/1/edit',
            '/admin/link-subsections', '/admin/link-subsections/1/edit',
            '/admin/page-contents', '/admin/contact-messages', '/admin/users',
        ];

        foreach ($screens as $url) {
            $this->get($url)->assertOk();
        }

        // ogni tipo di sezione di Pubblicazioni
        foreach (\App\Models\PublicationSection::pluck('id') as $id) {
            $this->get("/admin/publication-sections/{$id}/edit")->assertOk();
        }
        foreach (\App\Models\PageContent::pluck('id') as $id) {
            $this->get("/admin/page-contents/{$id}/edit")->assertOk();
        }
    }
}
