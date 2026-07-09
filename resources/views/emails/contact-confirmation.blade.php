<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Recibimos tu solicitud</title>
</head>
<body style="margin:0; padding:0; background-color:#0f0f0f; font-family: Arial, Helvetica, sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#0f0f0f; padding:32px 16px;">
<tr>
<td align="center">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px; background-color:#1a1a1a; border-radius:8px; overflow:hidden;">

    {{-- Header --}}
    <tr>
        <td style="background-color:#000000; padding:28px 32px; text-align:center;">
            <p style="margin:0; color:#DCA54A; font-size:20px; font-weight:bold; letter-spacing:2px;">CAPETILLO</p>
            <p style="margin:2px 0 0; color:#DCA54A; font-size:11px; letter-spacing:4px;">P R O D U C C I O N E S</p>
        </td>
    </tr>

    {{-- Contenido --}}
    <tr>
        <td style="padding:36px 32px;">

            <p style="margin:0 0 4px; color:#DCA54A; font-size:12px; text-transform:uppercase; letter-spacing:1px;">
                ¡Solicitud recibida!
            </p>
            <h1 style="margin:0 0 20px; color:#ffffff; font-size:22px; font-weight:bold;">
                Hola {{ $contactMessage->name }},
            </h1>

            <p style="margin:0 0 16px; color:#d5d5d5; font-size:14px; line-height:1.7;">
                Gracias por contactar a <strong style="color:#ffffff;">Capetillo Producciones</strong>. Hemos recibido
                tu solicitud correctamente y nuestro equipo se pondrá en contacto contigo a la brevedad.
            </p>

            @if ($contactMessage->talent_name)
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#262626; border-left:3px solid #DCA54A; border-radius:4px; margin:20px 0;">
                <tr>
                    <td style="padding:14px 20px;">
                        <p style="margin:0 0 4px; color:#999999; font-size:11px; text-transform:uppercase; letter-spacing:1px;">Talento de interés</p>
                        <p style="margin:0; color:#DCA54A; font-size:16px; font-weight:bold;">{{ $contactMessage->talent_name }}</p>
                    </td>
                </tr>
            </table>
            @endif

            <p style="margin:20px 0 16px; color:#d5d5d5; font-size:14px; line-height:1.7;">
                Mientras tanto, puedes ayudarnos a preparar una cotización más precisa completando algunos datos
                adicionales sobre tu evento. Usa el siguiente enlace también para <strong style="color:#ffffff;">dar seguimiento</strong>
                al estado de tu solicitud en cualquier momento:
            </p>

            {{-- Botón de acción --}}
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin:28px 0;">
                <tr>
                    <td align="center">
                        <a href="{{ $link }}" style="display:inline-block; background-color:#DCA54A; color:#1a1a1a; text-decoration:none; font-size:14px; font-weight:bold; padding:14px 36px; border-radius:4px;">
                            Completar datos y dar seguimiento
                        </a>
                    </td>
                </tr>
            </table>

            <p style="margin:0 0 4px; color:#777777; font-size:11px; line-height:1.6; word-break:break-all;">
                O copia y pega este enlace en tu navegador:<br>
                <a href="{{ $link }}" style="color:#DCA54A; text-decoration:none;">{{ $link }}</a>
            </p>

        </td>
    </tr>

    {{-- Footer --}}
    <tr>
        <td style="background-color:#000000; padding:20px 32px; text-align:center;">
            <p style="margin:0 0 4px; color:#999999; font-size:12px;">Saludos,</p>
            <p style="margin:0 0 12px; color:#DCA54A; font-size:13px; font-weight:bold;">Capetillo Producciones</p>
            <p style="margin:0; color:#555555; font-size:11px;">
                ventas@capetilloproducciones.mx · www.capetilloproducciones.mx
            </p>
        </td>
    </tr>

</table>
</td>
</tr>
</table>
</body>
</html>