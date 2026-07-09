<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Nueva solicitud de contratación</title>
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

    {{-- Banda de alerta --}}
    <tr>
        <td style="background-color:#DCA54A; padding:10px 32px; text-align:center;">
            <p style="margin:0; color:#1a1a1a; font-size:13px; font-weight:bold; letter-spacing:1px;">
                NUEVA SOLICITUD DE CONTRATACIÓN
            </p>
        </td>
    </tr>

    {{-- Contenido --}}
    <tr>
        <td style="padding:32px;">

            <p style="margin:0 0 24px; color:#e5e5e5; font-size:14px; line-height:1.6;">
                Se ha recibido una nueva solicitud a través del formulario de contacto del sitio web.
            </p>

            {{-- Talento --}}
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#262626; border-left:3px solid #DCA54A; border-radius:4px; margin-bottom:24px;">
                <tr>
                    <td style="padding:16px 20px;">
                        <p style="margin:0 0 4px; color:#999999; font-size:11px; text-transform:uppercase; letter-spacing:1px;">Talento solicitado</p>
                        <p style="margin:0; color:#DCA54A; font-size:18px; font-weight:bold;">{{ $data['talent_name'] ?: 'No especificado' }}</p>
                    </td>
                </tr>
            </table>

            {{-- Datos del cliente --}}
            <p style="margin:0 0 12px; color:#DCA54A; font-size:12px; font-weight:bold; text-transform:uppercase; letter-spacing:1px; border-bottom:1px solid #333333; padding-bottom:8px;">
                Datos del cliente
            </p>

            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin-bottom:24px;">
                <tr>
                    <td style="padding:6px 0; color:#999999; font-size:13px; width:130px; vertical-align:top;">Nombre</td>
                    <td style="padding:6px 0; color:#f5f5f5; font-size:13px; font-weight:bold;">{{ $data['name'] }}</td>
                </tr>
                <tr>
                    <td style="padding:6px 0; color:#999999; font-size:13px; vertical-align:top;">Correo</td>
                    <td style="padding:6px 0; color:#f5f5f5; font-size:13px;">
                        <a href="mailto:{{ $data['email'] }}" style="color:#f5f5f5; text-decoration:none;">{{ $data['email'] }}</a>
                    </td>
                </tr>
                <tr>
                    <td style="padding:6px 0; color:#999999; font-size:13px; vertical-align:top;">Teléfono</td>
                    <td style="padding:6px 0; color:#f5f5f5; font-size:13px;">
                        @if (!empty($data['phone']))
                            <a href="tel:{{ $data['phone'] }}" style="color:#f5f5f5; text-decoration:none;">{{ $data['phone'] }}</a>
                        @else
                            No proporcionado
                        @endif
                    </td>
                </tr>
                @if (!empty($data['estado_republica']))
                <tr>
                    <td style="padding:6px 0; color:#999999; font-size:13px; vertical-align:top;">Estado</td>
                    <td style="padding:6px 0; color:#f5f5f5; font-size:13px;">{{ $data['estado_republica'] }}</td>
                </tr>
                @endif
                @if (!empty($data['ciudad']))
                <tr>
                    <td style="padding:6px 0; color:#999999; font-size:13px; vertical-align:top;">Ciudad</td>
                    <td style="padding:6px 0; color:#f5f5f5; font-size:13px;">{{ $data['ciudad'] }}</td>
                </tr>
                @endif
                @if (!empty($data['venue']))
                <tr>
                    <td style="padding:6px 0; color:#999999; font-size:13px; vertical-align:top;">Venue</td>
                    <td style="padding:6px 0; color:#f5f5f5; font-size:13px;">{{ $data['venue'] }}</td>
                </tr>
                @endif
                @if (!empty($data['aforo_esperado']))
                <tr>
                    <td style="padding:6px 0; color:#999999; font-size:13px; vertical-align:top;">Aforo esperado</td>
                    <td style="padding:6px 0; color:#f5f5f5; font-size:13px;">{{ number_format($data['aforo_esperado']) }} personas</td>
                </tr>
                @endif
                @if (!empty($data['tipo_evento']))
                <tr>
                    <td style="padding:6px 0; color:#999999; font-size:13px; vertical-align:top;">Tipo de evento</td>
                    <td style="padding:6px 0; color:#f5f5f5; font-size:13px;">
                        {{ [
                            'privado' => 'Privado',
                            'corporativo' => 'Corporativo',
                            'publico_masivo' => 'Público / Masivo',
                            'con_venta_boletos' => 'Con venta de boletos',
                            'social' => 'Social (boda / XV)',
                            'gubernamental' => 'Gubernamental',
                        ][$data['tipo_evento']] ?? $data['tipo_evento'] }}
                    </td>
                </tr>
                @endif
                @if (isset($data['tiene_presupuesto']))
                <tr>
                    <td style="padding:6px 0; color:#999999; font-size:13px; vertical-align:top;">Presupuesto</td>
                    <td style="padding:6px 0; color:#f5f5f5; font-size:13px;">
                        {{ $data['tiene_presupuesto'] ? 'Sí tiene presupuesto' : 'No tiene presupuesto' }}
                        @if (!empty($data['presupuesto_aproximado']))
                            — ${{ number_format((float) $data['presupuesto_aproximado'], 2) }} MXN
                        @endif
                    </td>
                </tr>
                @endif
            </table>

            {{-- Mensaje --}}
            <p style="margin:0 0 12px; color:#DCA54A; font-size:12px; font-weight:bold; text-transform:uppercase; letter-spacing:1px; border-bottom:1px solid #333333; padding-bottom:8px;">
                Mensaje
            </p>
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#262626; border-radius:4px; margin-bottom:28px;">
                <tr>
                    <td style="padding:16px 20px; color:#e5e5e5; font-size:13px; line-height:1.6;">
                        {{ $data['message'] }}
                    </td>
                </tr>
            </table>

            @if (!empty($publicLink))
            {{-- Botón de acción --}}
            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td align="center">
                        <a href="{{ $publicLink }}" style="display:inline-block; background-color:#DCA54A; color:#1a1a1a; text-decoration:none; font-size:14px; font-weight:bold; padding:14px 32px; border-radius:4px;">
                            Ver solicitud completa
                        </a>
                    </td>
                </tr>
            </table>
            @endif

        </td>
    </tr>

    {{-- Footer --}}
    <tr>
        <td style="background-color:#000000; padding:20px 32px; text-align:center;">
            <p style="margin:0; color:#666666; font-size:11px;">
                {{ config('app.name') }} · ventas@capetilloproducciones.mx
            </p>
        </td>
    </tr>

</table>
</td>
</tr>
</table>
</body>
</html>