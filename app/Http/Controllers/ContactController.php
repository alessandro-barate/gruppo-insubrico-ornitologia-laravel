<?php

namespace App\Http\Controllers;
use App\Http\Requests\StoreContactRequest;
use App\Models\ContactMessage;
use App\Mail\ContactNotification;
use App\Mail\ContactConfirmation;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function store(StoreContactRequest $request)
    {

        // Honeypot: se il campo "website" è compilato, è un bot.
        // Risposta con 200 senza salvare nè inviare, così non si insospettisce il bot.
        if ($request->filled('website')) {
            return response()->json(['message' => 'Messaggio inviato con successo'], 201);
        }

        $contact = ContactMessage::create($request->validated());

        try {

            Mail::to('tua-mail@esempio.it')->send(new ContactNotification($contact));   // TODO: In produzione mettere indirizzo mail reale
            Mail::to($contact->mail)->send(new ContactConfirmation($contact));

        } catch (\Throwable $e) {

            Log::error('Invio mail fallito: ' . $e->getMessage());    // Il messaggio è comunque salvato: la richiesta non fallisce

        }

        return response()->json(['message' => 'Messaggio inviato con successo'], 201);
    }
}
