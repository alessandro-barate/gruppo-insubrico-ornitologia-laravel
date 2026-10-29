<?php

namespace Tests\Feature;

use App\Mail\ContactConfirmation;
use App\Mail\ContactNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class FormsTest extends TestCase
{
    use RefreshDatabase;

    private array $contact = [
        'name' => 'Mario', 'surname' => 'Rossi', 'mail' => 'mario@example.com',
        'message' => 'Un messaggio di prova più lungo di 255 caratteri. '.'x',
    ];

    public function test_contact_saves_and_sends_both_mails(): void
    {
        Mail::fake();
        config(['gio.contact_to' => 'segreteria@example.com']);

        $data = $this->contact;
        $data['message'] = str_repeat('Testo lungo. ', 40); // > 255 caratteri

        $this->postJson('/api/contact', $data)->assertCreated();

        $this->assertDatabaseCount('contact_messages', 1);
        Mail::assertSent(ContactNotification::class, fn ($m) => $m->hasTo('segreteria@example.com'));
        Mail::assertSent(ContactConfirmation::class, fn ($m) => $m->hasTo('mario@example.com'));
    }

    public function test_contact_honeypot_and_validation(): void
    {
        Mail::fake();

        $this->postJson('/api/contact', $this->contact + ['website' => 'spam'])->assertCreated();
        $this->assertDatabaseCount('contact_messages', 0);

        $this->postJson('/api/contact', ['name' => 'x'])->assertStatus(422);
        Mail::assertNothingSent();
    }

    public function test_newsletter_subscribe(): void
    {
        config(['services.mailchimp.key' => 'abc-us21', 'services.mailchimp.list_id' => 'list1']);
        Http::fake(['https://us21.api.mailchimp.com/*' => Http::response(['status' => 'pending'], 200)]);

        $this->postJson('/api/newsletter/subscribe', ['email' => 'Mario@Example.com', 'privacy' => true])
            ->assertOk()->assertJsonStructure(['message']);

        Http::assertSent(fn ($r) => $r->method() === 'PUT'
            && str_contains($r->url(), '/lists/list1/members/'.md5('mario@example.com'))
            && $r['status_if_new'] === 'pending');
    }

    public function test_newsletter_errors(): void
    {
        config(['services.mailchimp.key' => 'abc-us21', 'services.mailchimp.list_id' => 'list1']);

        // consenso mancante
        $this->postJson('/api/newsletter/subscribe', ['email' => 'a@example.com'])
            ->assertStatus(422)->assertJsonPath('message', 'Per iscriverti devi dare il consenso.');

        // indirizzo rifiutato da Mailchimp
        Http::fake(['*' => Http::response(['title' => 'Invalid Resource'], 400)]);
        $this->postJson('/api/newsletter/subscribe', ['email' => 'b@example.com', 'privacy' => true])
            ->assertStatus(422);

        // bot
        $this->postJson('/api/newsletter/subscribe', ['website' => 'x'])->assertOk();
    }

    public function test_newsletter_without_configuration(): void
    {
        config(['services.mailchimp.key' => null]);
        $this->postJson('/api/newsletter/subscribe', ['email' => 'a@example.com', 'privacy' => true])
            ->assertStatus(502);
    }
}
