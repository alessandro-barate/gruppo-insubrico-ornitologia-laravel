<?php

namespace Tests\Feature;

use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Le API devono restituire la stessa forma dei dati finti del frontend
 * (composables Nuxt). Il database parte dai contenuti attuali del sito.
 */
class PublicApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DatabaseSeeder::class);
    }

    public function test_news_list_is_paginated_newest_first(): void
    {
        $res = $this->getJson('/api/news?page=1&perPage=3')->assertOk()
            ->assertJsonStructure(['data' => [['id', 'slug', 'title', 'date', 'attachments']], 'total', 'page', 'perPage'])
            ->assertJsonPath('perPage', 3);

        $dates = array_column($res->json('data'), 'date');
        $sorted = $dates;
        rsort($sorted);
        $this->assertSame($sorted, $dates);
    }

    public function test_news_detail_and_missing_news(): void
    {
        $this->getJson('/api/news/censimento-avifauna-2025')->assertOk()
            ->assertJsonPath('slug', 'censimento-avifauna-2025');
        $this->getJson('/api/news/non-esiste')->assertNotFound()->assertJsonStructure(['message']);
    }

    public function test_publications_home_and_tree(): void
    {
        $this->getJson('/api/pubblicazioni')->assertOk()
            ->assertJsonStructure(['intro_text', 'cards' => [['slug', 'title', 'type']]]);

        $this->getJson('/api/pubblicazioni/uccelli-varese')->assertOk()
            ->assertJsonPath('node.type', 'group')
            ->assertJsonCount(2, 'childCards');

        $this->getJson('/api/pubblicazioni/uccelli-varese/resoconti-ornitologici')->assertOk()
            ->assertJsonPath('node.type', 'pdf-list')
            ->assertJsonPath('crumbs.1.to', '/pubblicazioni/uccelli-varese/resoconti-ornitologici');

        $this->getJson('/api/pubblicazioni/quaderni/quaderno-5-2026')->assertOk()
            ->assertJsonPath('node.type', 'detail')
            ->assertJsonStructure(['node' => ['inner_pages' => [['id', 'image_path']]]]);

        $this->getJson('/api/pubblicazioni/quaderni/non-esiste')->assertNotFound();
    }

    public function test_bibliography_items_link_their_pdf(): void
    {
        $items = $this->getJson('/api/pubblicazioni/altre-pubblicazioni')->assertOk()->json('node.items');

        $this->assertCount(68, $items);
        $withPdf = array_filter($items, fn ($i) => isset($i['pdf_url']));
        $this->assertCount(67, $withPdf);
    }

    public function test_projects(): void
    {
        $this->getJson('/api/progetti')->assertOk()->assertJsonStructure(['intro_text', 'gallery' => [['src', 'alt']]]);
        $this->getJson('/api/progetti/sections')->assertOk()->assertJsonCount(3);

        $this->getJson('/api/progetti/ricerca-in-corso')->assertOk()
            ->assertJsonPath('kind', 'section')
            ->assertJsonStructure(['section', 'projects', 'projectCards' => [['slug', 'title']]]);

        $this->getJson('/api/progetti/ricerca-in-corso/iwc')->assertOk()
            ->assertJsonPath('kind', 'project')
            ->assertJsonPath('project.section_slug', 'ricerca-in-corso');

        // Sezione a 2 livelli: niente pagina per il singolo progetto
        $this->getJson('/api/progetti/ricerca-terminati/qualcosa')->assertNotFound();
    }

    public function test_outreach_and_links(): void
    {
        $this->getJson('/api/divulgazione')->assertOk()->assertJsonStructure(['intro_text']);
        $this->getJson('/api/divulgazione/subsections')->assertOk()
            ->assertJsonStructure([['slug', 'title', 'title_parts', 'aria_label']]);
        $this->getJson('/api/divulgazione/eventi')->assertOk()->assertJsonStructure(['items' => [['id', 'title']]]);

        $this->getJson('/api/links')->assertOk()
            ->assertJsonStructure(['portali', 'organizzazioni', 'reti', 'turismo'])
            ->assertJsonStructure(['portali' => [['title', 'groups' => [['links' => [['title', 'href']]]]]]]);
    }

    public function test_page_contents(): void
    {
        $this->getJson('/api/pages/news')->assertOk()->assertJsonStructure(['title', 'intro', 'hero_image']);
    }

    public function test_search(): void
    {
        $this->getJson('/api/search?q=r')->assertOk()->assertJsonPath('total', 0);

        $res = $this->getJson('/api/search?q=goosander&limit=5')->assertOk();
        $this->assertGreaterThan(0, $res->json('total'));
        $this->assertSame('bibliografia', $res->json('data.0.type'));

        // accenti e un errore di battitura
        $this->assertGreaterThan(0, $this->getJson('/api/search?q=perche')->json('total'));
        $this->assertGreaterThan(0, $this->getJson('/api/search?q=migrazone')->json('total'));

        // le parole trovate restano stringhe, anche se sono numeri
        $terms = $this->getJson('/api/search?q=bol+2021')->json('data.0.terms');
        $this->assertContains('2021', $terms);
        foreach ($terms as $t) {
            $this->assertIsString($t);
        }
    }

    public function test_search_index_follows_changes_from_the_panel(): void
    {
        $this->assertSame(0, $this->getJson('/api/search?q=zigolofantastico')->json('total'));

        \App\Models\News::create([
            'slug' => 'prova', 'title' => 'Zigolofantastico avvistato', 'date' => now(),
        ]);

        $this->assertSame(1, $this->getJson('/api/search?q=zigolofantastico')->json('total'));
    }
}
