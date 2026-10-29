<?php

namespace Tests\Unit;

use App\Support\RichHtml;
use PHPUnit\Framework\TestCase;

class HelpersTest extends TestCase
{
    public function test_rich_html_adds_frontend_classes(): void
    {
        $this->assertSame(
            '<h2 class="subtitle">T</h2><ul class="specific-list"><li>a</li></ul><a class="specific-link" href="/x">l</a><hr class="space-line">',
            RichHtml::render('<h2>T</h2><ul><li>a</li></ul><a href="/x">l</a><hr>')
        );
        // classi già presenti e tag simili restano come sono
        $this->assertSame('<ul class="x"></ul><abbr>b</abbr>', RichHtml::render('<ul class="x"></ul><abbr>b</abbr>'));
        $this->assertNull(RichHtml::render(null));
    }
}
