<?php

namespace App\Services;

use App\Models\LinkSubsection;
use App\Models\News;
use App\Models\OutreachSection;
use App\Models\ProjectSection;
use App\Models\PublicationSection;
use App\Support\FileUrl;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

/**
 * Ricerca nel sito (GET /api/search). Stessa forma di risposta del mock
 * Nuxt (server/api/search.get.ts): { q, total, data: SearchResult[] }.
 *
 * I contenuti sono pochi (qualche centinaio di voci), quindi l'indice
 * si costruisce in PHP e resta in cache finché un contenuto non cambia
 * (vedi AppServiceProvider). Si cerca in titoli e testi, non dentro i PDF.
 *
 * Regole come nel frontend:
 *  - maiuscole e accenti ignorati ("perche" trova "perché")
 *  - ogni parola cercata deve comparire (AND)
 *  - una parola trova anche le parole che iniziano così ("ron" → "rondone")
 *  - con parole di 5+ lettere è tollerato un errore di battitura
 *  - il titolo pesa più del testo
 */
class SiteSearch
{
    public const CACHE_KEY = 'site-search.documents';

    public const TYPE_LABELS = [
        'pagina' => 'Pagine',
        'news' => 'News',
        'progetto' => 'Progetti',
        'pubblicazione' => 'Pubblicazioni',
        'bibliografia' => 'Bibliografia',
        'divulgazione' => 'Divulgazione',
        'link' => 'Link utili',
    ];

    private const FIELD_WEIGHTS = ['title' => 3.0, 'context' => 1.2, 'text' => 1.0];

    // Pagine con il testo scritto nei template del frontend: descrizione
    // con le parole che un visitatore potrebbe cercare.
    private const STATIC_PAGES = [
        ['Home', '/', 'Gruppo Insubrico di Ornitologia G.I.O. Associazione per lo studio e la conservazione degli uccelli in provincia di Varese. Le news e le attività del G.I.O.'],
        ['Chi siamo', '/chi-siamo', 'La storia, la missione e il Consiglio Direttivo del Gruppo Insubrico di Ornitologia, Organizzazione di Volontariato (ODV) con sede al Civico Museo Insubrico di Storia Naturale di Clivio. Diventa socio: iscrizione annuale, quota, tessera socio, riunioni mensili, assemblea, comunicazioni tra i soci, donazioni.'],
        ['News', '/news', 'Notizie e aggiornamenti sulle attività del G.I.O.: eventi, progetti, monitoraggi, iniziative e pubblicazioni.'],
        ['Progetti', '/progetti', 'Progetti di ricerca in corso e terminati, monitoraggi, censimenti e atlanti ornitologici in provincia di Varese.'],
        ['Divulgazione', '/divulgazione', 'Corsi di birdwatching e ornitologia, serate divulgative, incontri, convegni ed eventi organizzati dal G.I.O.'],
        ['Pubblicazioni', '/pubblicazioni', 'Quaderni del G.I.O., BOL Bollettino Ornitologico Lombardo, Uccelli della Provincia di Varese, check-list, resoconti ornitologici, bibliografia e risultati delle ricerche.'],
        ['Social e link utili', '/links', "Seguici su Facebook e Instagram, iscriviti alla newsletter. Link utili e risorse per l'ornitologia e il birdwatching."],
        ['Form di contatto', '/form', 'Contattaci, scrivici una mail, informazioni, richiesta copie dei Quaderni, donazioni.'],
        ['Modulo di iscrizione', '/module', 'Iscriviti al G.I.O., diventa socio, modulo di iscrizione annuale.'],
    ];

    /** @return array{total: int, data: array<int, array>} */
    public function search(string $q, int $limit): array
    {
        $terms = $this->tokenize($q);
        if (! $terms) {
            return ['total' => 0, 'data' => []];
        }

        $hits = [];
        foreach ($this->documents() as $doc) {
            $score = 0.0;
            $matched = [];

            foreach ($terms as $term) {
                $best = 0.0;
                foreach (self::FIELD_WEIGHTS as $field => $weight) {
                    // Campi lunghi pesano un po' meno: una parola in un titolo
                    // breve conta più della stessa parola in una citazione lunga
                    $norm = 1 / (1 + 0.1 * sqrt(count($doc['tokens'][$field])));
                    foreach ($doc['tokens'][$field] as $token) {
                        $s = $this->matchScore($term, $token);
                        if ($s > 0) {
                            $matched[$token] = true;
                            $best = max($best, $s * $weight * $norm);
                        }
                    }
                }
                if ($best === 0.0) {
                    continue 2; // una parola non c'è: documento scartato (AND)
                }
                $score += $best;
            }

            // array_keys trasforma "2021" in numero: le parole restano stringhe
            $hits[] = ['doc' => $doc, 'score' => $score, 'terms' => array_map('strval', array_keys($matched))];
        }

        usort($hits, fn ($a, $b) => $b['score'] <=> $a['score']);

        $data = array_map(fn ($hit) => $this->toResult($hit['doc'], $hit['terms']), array_slice($hits, 0, $limit));

        return ['total' => count($hits), 'data' => $data];
    }

    public static function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    // ─── Indice ──────────────────────────────────────────

    private function documents(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            $docs = [];

            foreach (self::STATIC_PAGES as [$title, $url, $text]) {
                $docs[] = $this->doc('pagina-'.$url, 'pagina', $title, $text, null, null, $url, 'page');
            }

            foreach (News::published()->get() as $n) {
                $docs[] = $this->doc(
                    'news-'.$n->slug, 'news', $n->title, $n->excerpt.' '.$n->body,
                    $n->category ? "News › {$n->category}" : 'News',
                    $n->date?->year, '/news/'.$n->slug, 'page',
                );
            }

            $this->collectProjects($docs);

            $roots = PublicationSection::whereNull('parent_id')->ordered()->get();
            $this->collectPublications($roots, '/pubblicazioni', 'Pubblicazioni', $docs);

            foreach (OutreachSection::with('items')->ordered()->get() as $section) {
                $url = '/divulgazione/'.$section->slug;
                $docs[] = $this->doc("div-{$section->slug}", 'divulgazione', $section->title,
                    $section->intro_excerpt.' '.$section->intro_text, 'Divulgazione', null, $url, 'page');

                foreach ($section->items as $item) {
                    $docs[] = $this->doc("div-{$section->slug}-{$item->id}", 'divulgazione', $item->title,
                        $item->body, "Divulgazione › {$section->title}",
                        is_numeric($item->year) ? (int) $item->year : null, $url, 'page');
                }
            }

            foreach (LinkSubsection::ordered()->get() as $sub) {
                foreach ($sub->groups ?? [] as $group) {
                    foreach ($group['links'] ?? [] as $link) {
                        if (empty($link['href'])) {
                            continue;
                        }
                        $docs[] = $this->doc('link-'.$link['href'], 'link', trim($link['title'] ?? ''),
                            preg_replace('/^\s*[-–]\s*/u', '', $link['subtitle'] ?? ''),
                            implode(' › ', array_filter([$sub->title, $group['title'] ?? null])),
                            null, $link['href'], 'external');
                    }
                }
            }

            // id univoci: in caso di doppioni resta il primo
            $unique = [];
            foreach ($docs as $doc) {
                if ($doc['title'] !== '' && ! isset($unique[$doc['id']])) {
                    $unique[$doc['id']] = $doc;
                }
            }

            return array_values($unique);
        });
    }

    private function collectProjects(array &$docs): void
    {
        foreach (ProjectSection::with('projects')->ordered()->get() as $section) {
            $sectionUrl = '/progetti/'.$section->slug;
            $docs[] = $this->doc("prog-{$section->slug}", 'progetto', $section->title,
                $section->intro_excerpt.' '.$section->intro_text, 'Progetti', null, $sectionUrl, 'page');

            $descriptionOnly = in_array($section->slug, ProjectSection::DESCRIPTION_ONLY, true);

            foreach ($section->projects as $project) {
                $url = $section->projects_are_navigable ? "{$sectionUrl}/{$project->slug}" : $sectionUrl;
                $blocks = $project->apiBlocks();
                if ($descriptionOnly) {
                    $blocks = array_values(array_filter($blocks, fn ($b) => empty($b['links'])));
                }

                $docs[] = $this->doc("prog-{$section->slug}-{$project->id}", 'progetto', $project->title,
                    $project->intro.' '.implode(' ', array_column($blocks, 'text')),
                    "Progetti › {$section->title}", null, $url, 'page');

                foreach ($blocks as $b => $block) {
                    foreach ($block['links'] ?? [] as $l => $link) {
                        $docs[] = $this->doc("prog-{$project->id}-{$b}-{$l}", 'progetto', $link['label'], '',
                            "Progetti › {$project->title}", null, $link['url'], $this->target($link['url']));
                    }
                }
            }
        }
    }

    private function collectPublications($nodes, string $parentPath, string $parentContext, array &$docs): void
    {
        foreach ($nodes as $node) {
            $node->loadMissing(['children', 'items']);
            $path = "{$parentPath}/{$node->slug}";
            $context = "{$parentContext} › {$node->title}";

            $text = implode(' ', array_filter([$node->intro_excerpt, $node->intro_text, $node->second_intro_text]));
            if ($node->type === 'detail') {
                $text .= ' '.$node->body;
            }
            $docs[] = $this->doc("pub-{$path}", 'pubblicazione', $node->title, $text, $parentContext, null, $path, 'page');

            switch ($node->type) {
                case 'group':
                    $this->collectPublications($node->children, $path, $context, $docs);
                    break;

                case 'pdf-list':
                    foreach ($node->items as $item) {
                        $url = FileUrl::to($item->file_path);
                        if (! $url) {
                            continue;
                        }
                        $title = $item->year
                            ? "{$node->title} {$item->year} – {$item->title}"
                            : "{$node->title} – {$item->title}";
                        $docs[] = $this->doc("pub-{$path}-pdf-{$item->id}", 'pubblicazione', $title, '',
                            $context, $item->year, $url, $this->target($url));
                    }
                    break;

                case 'bibliography':
                    foreach ($node->items as $item) {
                        $citation = $this->plain($item->text);
                        if ($citation === '') {
                            continue;
                        }
                        preg_match('/\b(19|20)\d{2}\b/', $citation, $m);
                        $url = FileUrl::to($item->file_path) ?? $path;
                        // la citazione intera fa da titolo (niente estratto)
                        $docs[] = $this->doc("pub-{$path}-bib-{$item->id}", 'bibliografia', $citation, '',
                            $context, isset($m[0]) ? (int) $m[0] : null, $url,
                            $item->file_path ? $this->target($url) : 'page');
                    }
                    break;

                case 'cards':
                    foreach ($node->items as $item) {
                        $docs[] = $this->doc("pub-{$path}-card-{$item->id}", 'pubblicazione', (string) $item->title,
                            $item->text, $context, null, $path, 'page');
                    }
                    break;
            }
        }
    }

    private function doc(string $id, string $type, ?string $title, ?string $text, ?string $context, ?int $year, string $url, string $target): array
    {
        $title = $this->plain($title);
        $text = $this->plain($text);

        return [
            'id' => $id,
            'type' => $type,
            'title' => $title,
            'text' => $text,
            'context' => $context,
            'year' => $year,
            'url' => $url,
            'target' => $target,
            'tokens' => [
                'title' => $this->tokenize($title),
                'context' => $this->tokenize((string) $context),
                'text' => $this->tokenize($text),
            ],
        ];
    }

    private function toResult(array $doc, array $terms): array
    {
        return array_filter([
            'id' => $doc['id'],
            'type' => $doc['type'],
            'type_label' => self::TYPE_LABELS[$doc['type']],
            'title' => $doc['title'],
            'excerpt' => $this->excerpt($doc['text'], $terms),
            'context' => $doc['context'],
            'year' => $doc['year'],
            'url' => $doc['url'],
            'target' => $doc['target'],
            'terms' => $terms,
        ], fn ($v) => $v !== null);
    }

    // ─── Testo ───────────────────────────────────────────

    private function target(string $url): string
    {
        return Str::startsWith($url, ['http://', 'https://']) && ! Str::contains($url, '/storage/')
            ? 'external'
            : 'file';
    }

    /** HTML → testo semplice su una riga */
    private function plain(?string $html): string
    {
        $text = preg_replace('/<(br|\/p|\/li|\/h\d|\/ul)\s*\/?>/i', ' ', (string) $html);
        $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');

        return trim(preg_replace('/\s+/u', ' ', $text));
    }

    /** minuscolo e senza accenti */
    private function fold(string $s): string
    {
        return mb_strtolower(Str::ascii($s));
    }

    /** @return string[] parole di almeno 2 caratteri */
    private function tokenize(string $s): array
    {
        preg_match_all('/[\p{L}\p{N}]{2,}/u', $this->fold($s), $m);

        return array_values(array_unique($m[0]));
    }

    /** 1 = uguale, 0.8 = inizio di parola, 0.5 = un errore di battitura */
    private function matchScore(string $term, string $token): float
    {
        if ($term === $token) {
            return 1.0;
        }
        if (str_starts_with($token, $term)) {
            return 0.8;
        }
        if (strlen($term) > 4 && abs(strlen($term) - strlen($token)) <= 1 && levenshtein($term, $token) <= 1) {
            return 0.5;
        }

        return 0.0;
    }

    /** Pezzo di testo attorno alla prima parola trovata */
    private function excerpt(string $text, array $terms, int $max = 170): ?string
    {
        if ($text === '') {
            return null;
        }

        // Str::ascii mantiene la lunghezza per le lettere accentate italiane:
        // le posizioni nel testo "piegato" valgono anche per l'originale
        $folded = mb_strtolower(Str::ascii($text));
        $pos = null;
        foreach ($terms as $term) {
            if (preg_match('/(^|[^\p{L}\p{N}])'.preg_quote($term, '/').'/u', $folded, $m, PREG_OFFSET_CAPTURE)) {
                $p = $m[0][1] + strlen($m[1][0]);
                $pos = $pos === null ? $p : min($pos, $p);
            }
        }

        $length = mb_strlen($text);
        if ($pos === null || $pos <= 40 || $length <= $max || mb_strlen($folded) !== $length) {
            return $this->truncate($text, $max);
        }

        $charPos = mb_strlen(substr($folded, 0, $pos));
        $start = mb_strrpos(mb_substr($text, 0, max(0, $charPos - 40)), ' ');
        $start = $start === false ? 0 : $start + 1;

        return '…'.$this->truncate(mb_substr($text, $start), $max);
    }

    private function truncate(string $s, int $max): string
    {
        if (mb_strlen($s) <= $max) {
            return $s;
        }
        $cut = mb_substr($s, 0, $max);
        $space = mb_strrpos($cut, ' ');

        return rtrim($space !== false && $space > $max * 0.6 ? mb_substr($cut, 0, $space) : $cut).'…';
    }
}
