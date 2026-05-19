<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>Verifica tu correo · Huellas Felices</title>
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
                                Verificación de correo
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="background-color:#5a9e47; height:3px; font-size:0; line-height:0;">&nbsp;</td>
                    </tr>

                    <!-- contenido -->
                    <tr>
                        <td style="padding:32px 28px;">

                            <p style="margin:0 0 18px; font-size:15px; line-height:1.7; color:#1e2e1a;">
                                Hola{{ isset($user) && $user->name ? ', ' . $user->name : '' }}. 👋
                            </p>

                            <p style="margin:0 0 20px; font-size:15px; line-height:1.7; color:#1e2e1a;">
                                Hemos recibido una solicitud para cambiar la dirección de correo electrónico de tu
                                cuenta de
                                <strong style="color:#2d5a27;">Huellas Felices</strong>.
                            </p>

                            <div
                                style="background-color:#eef5e8; border:1px solid #c8d9be; border-radius:12px; padding:18px; margin-bottom:24px;">
                                <p style="margin:0; font-size:14px;d line-height:1.6; color:#2d5a27;">
                                    Para confirmar el cambio, pulsa el botón deabajo.
                                    Así podremos enviarte avisos importantes sobre tus mascotas, estancias y facturas.
                                </p>
                            </div>

                            <!-- boton -->
                            <table role="presentation" cellspacing="0" cellpadding="0" style="margin:0 auto 24px;">
                                <tr>
                                    <td align="center" bgcolor="#3a7a2e" style="border-radius:12px;">
                                        <a href="{{ $url }}"
                                            style="display:inline-block; padding:13px 24px; font-size:14px; font-weight:bold; color:#f0ede6; text-decoration:none; border-radius:12px;">
                                            Confirmar correo
                                        </a>
                                    </td>
                                </tr>
                            </table>

                            <p style="margin:0 0 12px; font-size:13px; line-height:1.6; color:#8a8e84;">
                                Si no has solicitado este cambio o esta verificación, puedes ignorar este mensaje.
                            </p>

                            <p style="margin:0; font-size:12px; line-height:1.6; color:#8a8e84;">
                                Si el botón no funciona, copia y pega este enlace en tu navegador:
                            </p>

                            <p
                                style="margin:8px 0 0; font-size:12px; line-height:1.6; color:#5a9e47; word-break:break-all;">
                                {{ $url }}
                            </p>

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