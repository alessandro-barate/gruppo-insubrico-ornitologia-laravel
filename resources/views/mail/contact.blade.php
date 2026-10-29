@php $logoUrl = '/images/logo/gio-cerchio.png'; @endphp
{{-- 'https://gruppoinsubrico.com/images/logo/gio-cerchio.png' - mettere questo una volta in produzione --}}

<!DOCTYPE html>
<html lang="it" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="x-apple-disable-message-reformatting">
  <title>Nuovo messaggio dal form di contatto del sito</title>
  <!--[if !mso]><!-->
  <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;700&display=swap" rel="stylesheet">
  <!--<![endif]-->
  <!--[if mso]>
  <style type="text/css">
    table, td, div, p, a { font-family: Arial, Helvetica, sans-serif !important; }
  </style>
  <![endif]-->
  <style type="text/css">
    @import url('https://fonts.googleapis.com/css2?family=Sora:wght@400;700&display=swap');
    body { margin: 0; padding: 0; width: 100% !important; -webkit-text-size-adjust: 100%; }
    table { border-collapse: collapse; }
    img { border: 0; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; }
    a { color: #233162; }
    @media only screen and (max-width: 620px) {
      .wrapper { width: 100% !important; }
      .px { padding-left: 24px !important; padding-right: 24px !important; }
      .h1 { font-size: 24px !important; line-height: 30px !important; }
      .lbl { display:block !important; width:100% !important; padding:0 0 2px !important; }
      .val { display:block !important; width:100% !important; padding:0 0 14px !important; }
      .reply { text-align: center !important; }
    }
  </style>
</head>
<body style="margin:0; padding:0; background-color:#233162;">
  <div style="display:none; max-height:0; overflow:hidden; opacity:0; font-size:1px; line-height:1px; color:#ebf2fc;">
    Nuovo messaggio ricevuto dal form di contatto del sito.
  </div>
 
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#233162;">
    <tr>
      <td align="center" style="padding:32px 12px;">
 
        <table role="presentation" class="wrapper" width="600" cellpadding="0" cellspacing="0" style="width:600px; max-width:600px; background-color:#ffffff; border-radius:10px; overflow:hidden;">
 
          <!-- HEADER bianco a colore pieno -->
          <tr>
            <td bgcolor="#ebf2fc" align="center" style="background-color:#ebf2fc; padding:36px 24px;">
              <img src="{{ $logoUrl }}" width="177" height="104" alt="Gruppo Insubrico di Ornitologia" style="display:block; width:177px; height:104px; margin:0 auto 16px;">
              <div style="font-family:'Sora',Arial,Helvetica,sans-serif; font-size:18px; line-height:24px; color:#233162; font-weight:bold; letter-spacing:0.5px;">
                Gruppo Insubrico di Ornitologia
              </div>
            </td>
          </tr>
 
          <!-- TITOLO -->
          <tr>
            <td class="px" style="padding:36px 40px 4px;">
              <h1 class="h1" style="margin:0; font-family:'Sora',Arial,Helvetica,sans-serif; font-size:26px; line-height:32px; color:#233162; font-weight:bold;">
                Nuovo messaggio dal form di contatto del sito
              </h1>
              <p style="margin:8px 0 0; font-family:'Sora',Arial,Helvetica,sans-serif; font-size:15px; line-height:22px; color:#6a7385;">
                Hai ricevuto una nuova richiesta di contatto dal sito.
              </p>
            </td>
          </tr>
 
          <!-- DATI mittente -->
          <tr>
            <td class="px" style="padding:20px 40px 8px;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" bgcolor="#ebf2fc" style="background-color:#ebf2fc; border-radius:8px;">
                <tr>
                  <td style="padding:20px 24px; font-family:'Sora',Arial,Helvetica,sans-serif; font-size:15px; line-height:22px; color:#3a3a3a;">
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                      <tr>
                        <td class="lbl" width="90" valign="top" style="padding:0 0 12px; font-weight:bold; color:#233162;">Nome</td>
                        <td class="val" valign="top" style="padding:0 0 12px;">{{ $contact->name }} {{ $contact->surname }}</td>
                      </tr>
                      <tr>
                        <td class="lbl" width="90" valign="top" style="padding:0; font-weight:bold; color:#233162;">Email</td>
                        <td class="val" valign="top" style="padding:0;">
                          {{ $contact->mail }}
                        </td>
                        <td class="lbl reply" width="90" valign="top" style="padding:0; font-weight:bold; color:#233162;">
                          <button style="background-color: #233162; text-decoration: none; border: none; border-radius: 8px; padding: 0.5rem 2rem;">
                            <a href="mailto:{{ $contact->mail }}" style="color:#ffffff; text-decoration: none; font-family:'Sora',Arial,Helvetica,sans-serif;">Rispondi</a>
                          </button>
                        </td>
                      </tr>
                    </table>
                  </td>
                </tr>
              </table>
            </td>
          </tr>
 
          <!-- MESSAGGIO -->
          <tr>
            <td class="px" style="padding:16px 40px 36px; font-family:'Sora',Arial,Helvetica,sans-serif; font-size:16px; line-height:24px; color:#3a3a3a;">
              <p style="margin:0 0 8px; font-size:13px; text-transform:uppercase; letter-spacing:0.5px; color:#233162; font-weight:bold;">Messaggio</p>
              <p bgcolor="#ebf2fc" style="padding: 20px 24px; border-radius: 8px; margin:0; margin-bottom: 25px; background-color:#ebf2fc;">{{ $contact->message }}</p>
              <p style="margin:0 0 8px; font-size:13px; text-transform:uppercase; letter-spacing:0.5px; color:#233162; font-weight:bold;">Cosa fare</p>
              <p style="margin:0;">Clicca sul bottone "Rispondi" per rispondere direttamente al mittente.</p>
            </td>
          </tr>
 
          <!-- FOOTER -->
          <tr>
            <td bgcolor="#233162" align="center" style="background-color:#ebf2fc; padding:24px 40px; font-family:'Sora',Arial,Helvetica,sans-serif; font-size:12px; line-height:18px; color:#233162;">
              Gruppo Insubrico di Ornitologia - Notifica automatica dal form di contatto.
            </td>
          </tr>
 
        </table>
 
      </td>
    </tr>
  </table>
</body>
</html>