<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Factura disponible · Huellas Felices</title>
</head>

<body style="margin:0; padding:0; background-color:#f7f5f0; font-family:Arial, sans-serif; color:#1e2e1a;">

    <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
        style="background-color:#f7f5f0; padding:40px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                    style="max-width:580px; background-color:#ffffff; border-radius:16px; overflow:hidden; border:1px solid #d9ddd0;">

                    <!-- cabecera -->
                    <tr>
                        <td style="background-color:#1e2e1a; padding:28px 24px; text-align:center;">
                            <img src="{{ asset('images/logo.png') }}" alt="Huellas Felices"
                                style="max-height:60px; width:auto; display:block; margin:0 auto 12px;">
                            <h1 style="margin:0; font-size:24px; font-weight:600; color:#f0ede6;">
                                Residencia Huellas Felices
                            </h1>
                            <p style="margin:8px 0 0; font-size:14px; color:#9fcf8e;">
                               Factura Disponible
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color:#3a7abf; height:3px; font-size:0; line-height:0;">&nbsp;</td>
                    </tr>

                    <!-- contenido -->
                    <tr>
                        <td style="padding:32px 28px;">

                            <p style="margin:0 0 20px; font-size:15px; line-height:1.7; color:#1e2e1a;">
                                La estancia de
                                <strong style="color:#2d5a27;">{{ $estancia->mascota->nombre ?? 'tu mascota' }}</strong>
                                ha finalizado y ya tienes disponible el resumen de la factura.
                            </p>

                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                style="background-color:#f7f5f0; border:1px solid #d9ddd0; border-radius:10px; margin-bottom:24px;">
                                <tr>
                                    <td style="padding:12px 16px 6px; font-size:13px; color:#8a8e84;">
                                        Entrada
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:0 16px 12px; font-size:14px; font-weight:bold; color:#1e2e1a;">
                                        {{ date('d/m/Y', strtotime($estancia->fecha_entrada)) }}
                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        style="padding:0 16px 6px; border-top:1px solid #e8e5de; font-size:13px; color:#8a8e84; padding-top:12px;">
                                        Salida
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:0 16px 12px; font-size:14px; font-weight:bold; color:#1e2e1a;">
                                        {{ date('d/m/Y', strtotime($estancia->fechaSalidaVisible())) }}
                                    </td>
                                </tr>

                                <tr>
                                    <td
                                        style="padding:0 16px 6px; border-top:1px solid #e8e5de; font-size:13px; color:#8a8e84; padding-top:12px;">
                                        Total
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding:0 16px 12px; font-size:14px; font-weight:bold; color:#1e2e1a;">
                                        {{ number_format($estancia->totalConExtras(), 2) }} €
                                    </td>
                                </tr>
                            </table>

                            <div
                                style="background-color:#e6f0fb; border:1px solid #b0cef0; border-radius:12px; padding:16px; margin-bottom:24px;">
                                <p style="margin:0; font-size:14px; line-height:1.6; color:#1a4f8a;">
                                    Puedes consultar la factura entrando en la aplicación, dentro de
                                    <strong>Mis estancias → Factura</strong>.
                                </p>
                            </div>

                        </td>
                    </tr>

                    <!-- pie -->
                    <tr>
                        <td
                            style="background-color:#f7f5f0; border-top:1px solid #e8e5de; padding:18px 28px; text-align:center; font-size:11px; color:#8a8e84;">
                            Este correo ha sido generado automáticamente por Residencia Huellas Felices.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>

</html>