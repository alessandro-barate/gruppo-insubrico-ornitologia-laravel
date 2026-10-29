<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Iscrizione alla newsletter dal sito (POST /api/newsletter/subscribe).
 *
 * Il contatto va nell'audience Mailchimp del G.I.O. come "pending":
 * Mailchimp invia la mail di conferma (doppio opt-in). Aggiunta manuale
 * degli iscritti e invio delle newsletter si fanno da Mailchimp.
 *
 * Risposte (le legge composables/useNewsletter.ts nel frontend):
 *   200 { message }  iscrizione avviata
 *   422 { message }  dati non validi o indirizzo rifiutato da Mailchimp
 *   429              troppi tentativi (throttle sulla rotta)
 *   502 { message }  errore di Mailchimp
 */
class NewsletterController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        // Honeypot: i bot compilano il campo nascosto "website", gli utenti no
        if ($request->filled('website')) {
            return response()->json(['message' => 'ok']);
        }

        $data = $request->validate([
            'email'   => ['required', 'email:rfc', 'max:255'],
            'privacy' => ['accepted'],
        ], [
            'email.required'   => 'Inserisci un indirizzo email valido.',
            'email.email'      => 'Inserisci un indirizzo email valido.',
            'email.max'        => 'Inserisci un indirizzo email valido.',
            'privacy.accepted' => 'Per iscriverti devi dare il consenso.',
        ]);

        $apiKey = config('services.mailchimp.key');
        $listId = config('services.mailchimp.list_id');

        if (! $apiKey || ! $listId) {
            Log::error('Mailchimp non configurato: MAILCHIMP_API_KEY o MAILCHIMP_LIST_ID mancanti');

            return response()->json([
                'message' => 'Si è verificato un errore. Riprova più tardi.',
            ], 502);
        }

        // Il data center ("us21"…) è il suffisso della API key
        $dc = substr($apiKey, strrpos($apiKey, '-') + 1);

        $email = strtolower(trim($data['email']));
        $hash  = md5($email);

        // PUT = "crea o aggiorna": chi è già iscritto non viene toccato
        // né riceve errori
        $response = Http::withBasicAuth('anystring', $apiKey)
            ->timeout(10)
            ->put("https://{$dc}.api.mailchimp.com/3.0/lists/{$listId}/members/{$hash}", [
                'email_address' => $email,
                'status_if_new' => 'pending',
            ]);

        if ($response->successful()) {
            return response()->json([
                'message' => "Quasi fatto! Controlla la tua casella email per confermare l'iscrizione.",
            ]);
        }

        // 400 = indirizzo rifiutato da Mailchimp (finto, disiscritto in
        // modo definitivo, ecc.)
        if ($response->status() === 400) {
            return response()->json([
                'message' => 'Non è stato possibile iscrivere questo indirizzo. Verifica che sia corretto.',
            ], 422);
        }

        Log::error('Mailchimp subscribe error', [
            'status' => $response->status(),
            'body'   => $response->json(),
        ]);

        return response()->json([
            'message' => 'Si è verificato un errore. Riprova più tardi.',
        ], 502);
    }
}
