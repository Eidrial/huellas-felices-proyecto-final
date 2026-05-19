<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aviso importante · Huellas Felices</title>
</head>

<body
    style="margin:0; padding:0; background-color:#f7f5f0; font-family:Georgia, 'Times New Roman', serif; color:#1e2e1a;">

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
                                Aviso Importante
                            </p>
                        </td>
                    </tr>

                    <!-- franja de acento -->
                    <tr>
                        <td style="background-color:#c9342e; height:3px; font-size:0; line-height:0;">&nbsp;</td>
                    </tr>

                    <!-- contenido -->
                    <tr>
                        <td style="padding:32px 28px;">

                            <p
                                style="margin:0 0 20px; font-size:15px; line-height:1.7; color:#1e2e1a; font-family:Arial, sans-serif;">
                                Se ha registrado un aviso importante para la estancia de
                                <strong
                                    style="color:#2d5a27;">{{ $aviso->estancia->mascota->nombre ?? 'tu mascota' }}</strong>.
                            </p>

                            <!-- bloque del mensaje -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                style="background-color:#fceaea; border-left:3px solid #c9342e; border-radius:0 10px 10px 0; margin-bottom:24px;">
                                <tr>
                                    <td
                                        style="padding:16px 18px; font-size:14px; line-height:1.7; color:#1e2e1a; font-family:Arial, sans-serif;">
                                        {{ $aviso->mensaje }}
                                    </td>
                                </tr>
                            </table>

                            <!-- datos del aviso -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                                style="background-color:#f7f5f0; border:1px solid #d9ddd0; border-radius:10px; margin-bottom:24px;">
                                <tr>
                                    <td
                                        style="padding:12px 16px 6px; font-size:13px; color:#8a8e84; font-family:Arial, sans-serif;">
                                        Enviado por
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        style="padding:0 16px 12px; font-size:14px; font-weight:bold; color:#1e2e1a; font-family:Arial, sans-serif;">
                                        {{ $aviso->usuario ? ucfirst($aviso->usuario->role) : '—' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        style="padding:0 16px 6px; border-top:1px solid #e8e5de; font-size:13px; color:#8a8e84; font-family:Arial, sans-serif; padding-top:12px;">
                                        Fecha
                                    </td>
                                </tr>
                                <tr>
                                    <td
                                        style="padding:0 16px 12px; font-size:14px; font-weight:bold; color:#1e2e1a; font-family:Arial, sans-serif;">
                                        {{ $aviso->created_at ? $aviso->created_at->format('d/m/Y H:i') : '' }}
                                    </td>
                                </tr>
                            </table>

                            <p
                                style="margin:0; font-size:13px; line-height:1.6; color:#8a8e84; font-family:Arial, sans-serif;">
                                Puedes consultar este aviso en la aplicación, dentro de
                                <strong style="color:#2d5a27;">Mis estancias → Avisos</strong>.
                            </p>

                        </td>
                    </tr>

                    <!-- pie -->
                    <tr>
                        <td
                            style="background-color:#f7f5f0; border-top:1px solid #e8e5de; padding:18px 28px; text-align:center; font-size:11px; color:#8a8e84; font-family:Arial, sans-serif;">
                            Este correo ha sido generado automaticamente por Residencia Huellas Felices.
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>

</html>