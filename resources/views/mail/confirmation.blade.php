@php $logoUrl = '/images/logo/gio-cerchio.png'; @endphp
{{-- 'https://gruppoinsubrico.com/images/logo/gio-cerchio.png' - mettere questo una volta in produzione --}}

<!DOCTYPE html>
<html lang="it" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="x-apple-disable-message-reformatting">
  <title>Conferma ricezione messaggio</title>
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
      .h1 { font-size: 26px !important; line-height: 32px !important; }
    }
  </style>
</head>
<body style="margin:0; padding:0; background-color:#233162;">
  <!-- preheader (nascosto) -->
  <div style="display:none; max-height:0; overflow:hidden; opacity:0; font-size:1px; line-height:1px; color:#ebf2fc;">
    Abbiamo ricevuto il tuo messaggio e ti risponderemo il prima possibile.
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
            <td class="px" align="center" style="padding:36px 40px 8px;">
              <h1 class="h1" style="margin:0; font-family:'Sora',Arial,Helvetica,sans-serif; font-size:28px; line-height:34px; color:#233162; font-weight:bold;">
                Grazie per averci contattato!
              </h1>
            </td>
          </tr>

          <!-- CORPO -->
          <tr>
            <td class="px" style="padding:12px 40px 8px; font-family:'Sora',Arial,Helvetica,sans-serif; font-size:16px; line-height:24px; color:#3a3a3a;">
              <p style="margin:0 0 16px;">Ciao {{ $contact->name }},</p>
              <p style="margin:0 0 16px;">
                abbiamo ricevuto il tuo messaggio e ti risponderemo il prima possibile.
                Di seguito trovi una copia di quanto ci hai inviato.
              </p>
            </td>
          </tr>

          <!-- RIQUADRO messaggio (sfondo azzurro pieno) -->
          <tr>
            <td class="px" style="padding:8px 40px 24px;">
              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" bgcolor="#ebf2fc" style="background-color:#ebf2fc; border-radius:8px;">
                <tr>
                  <td style="padding:20px 24px; font-family:'Sora',Arial,Helvetica,sans-serif; font-size:15px; line-height:22px; color:#3a3a3a;">
                    <p style="margin:0 0 6px; font-size:13px; text-transform:uppercase; letter-spacing:0.5px; color:#233162; font-weight:bold;">Il tuo messaggio</p>
                    <p style="margin:0;">{{ $contact->message }}</p>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- CHIUSURA -->
          <tr>
            <td class="px" style="padding:0 40px 36px; font-family:'Sora',Arial,Helvetica,sans-serif; font-size:16px; line-height:24px; color:#3a3a3a;">
              <p style="margin:0;">Un saluto,<br>Il team del Gruppo Insubrico di Ornitologia</p>
            </td>
          </tr>

          <!-- FOOTER blu -->
          <tr>
            <td bgcolor="#233162" align="center" style="background-color:#ebf2fc; padding:24px 40px; font-family:'Sora',Arial,Helvetica,sans-serif; font-size:12px; line-height:18px; color:#233162;">
              Gruppo Insubrico di Ornitologia<br>
              Questa è una mail automatica di conferma, non è necessario rispondere.
            </td>
          </tr>

        </table>

      </td>
    </tr>
  </table>
</body>
</html>