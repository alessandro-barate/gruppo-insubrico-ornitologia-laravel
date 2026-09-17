<?php

use Illuminate\Support\Facades\Route;

use App\Models\ContactMessage;
use App\Mail\ContactNotification;
use App\Mail\ContactConfirmation;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/preview-mail', function () {
    // dati finti per l'anteprima della mail inviata dall'utente (non tocca il DB)
    $contact = new ContactMessage([
        'name'    => 'Mario',
        'surname' => 'Rossi',
        'mail'    => 'mario.rossi@esempio.it',
        'message' => 'Clicca sul bottone "Rispondi" per rispondere direttamente al mittente.',
    ]);

    return new ContactNotification($contact);
});

Route::get('/preview-confirmation', function(){
    // dati finti per l'anteprima della mail di conferma di avvenuta ricezione inviata all'utente (non tocca il DB)
    $contact = new ContactMessage([
        'name'    => 'Mario',
        'surname' => 'Rossi',
        'mail'    => 'mario.rossi@esempio.it',
        'message' => 'Questo è un messaggio di prova per vedere lo stile della mail di conferma della ricezione del messaggio del form.',
    ]);

    return new ContactConfirmation($contact);
});

// TODO: in produzione rimuovere i blocchi sopra e gli import
