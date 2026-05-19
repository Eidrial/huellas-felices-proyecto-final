<!doctype html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Recuperación de contraseña</title>
</head>

<body style="margin:0; padding:0; background-color:#f7f5f0; font-family:Arial, sans-serif; color:#1e2e1a;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
        style="background-color:#f7f5f0; padding:30px 0;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0"
                    style="max-width:600px; background-color:#ffffff; border:1px solid #d9ddd0; border-radius:16px; overflow:hidden;">

                    <!-- cabecera -->
                    <tr>
                        <td style="background-color:#1e2e1a; padding:28px 24px; text-align:center;">
                            <img src="{{ asset('images/logo.png') }}" alt="Huellas Felices"
                                style="max-height:60px; width:auto; display:block; margin:0 auto 12px;">
                            <h1 style="margin:0; font-size:24px; font-weight:600; color:#f0ede6;">
                                Residencia Huellas Felices
                            </h1>
                            <p style="margin:8px 0 0; font-size:14px; color:#9fcf8e;">
                                Recuperación de contraseña
                            </p>
                        </td>
                    </tr>

                    <!-- contenido -->
                    <tr>
                        <td style="padding:32px 28px;">
                            <p style="margin:0 0 16px; font-size:16px; line-height:1.6;">
                                Hola 👋
                            </p>

                            <p style="margin:0 0 16px; font-size:15px; line-height:1.7; color:#4f5f49;">
                                Hemos recibido una solicitud para restablecer la contraseña de tu cuenta en
                                <strong>Huellas Felices</strong>.
                            </p>

                            <p style="margin:0 0 24px; font-size:15px; line-height:1.7; color:#4f5f49;">
                                Si has sido tú, puedes continuar desde el siguiente botón:
                            </p>

                            <div style="text-align:center; margin:0 0 28px;">
                                <a href="{{ $url }}"
                                    style="display:inline-block; background-color:#3a7a2e; color:#f0ede6; text-decoration:none; font-size:15px; font-weight:600; padding:14px 24px; border-radius:12px;">
                                    Restablecer contraseña
                                </a>
                            </div>

                            <div
                                style="background-color:#eef5e8; border:1px solid #c8d9be; border-radius:12px; padding:16px; margin-bottom:24px;">
                                <p style="margin:0; font-size:14px; line-height:1.6; color:#2d5a27;">
                                    Este enlace estará disponible durante <strong>60 minutos</strong>.
                                </p>
                            </div>

                            <p style="margin:0 0 16px; font-size:14px; line-height:1.7; color:#6f756b;">
                                Si no has solicitado este cambio, no tienes que hacer nada. Tu cuenta seguirá protegida.
                            </p>

                            <p style="margin:0; font-size:14px; line-height:1.7; color:#6f756b;">
                                Si el botón no funciona, copia y pega este enlace en tu navegador:
                            </p>

                            <p
                                style="margin:12px 0 0; font-size:13px; line-height:1.6; color:#5a7c4e; word-break:break-all;">
                                {{ $url }}
                            </p>
                        </td>
                    </tr>

                    <!-- pie -->
                    <tr>
                        <td
                            style="background-color:#fafaf8; padding:18px 24px; text-align:center; font-size:12px; color:#8a8e84; border-top:1px solid #e8e5de;">
                            Un saludo, equipo de Huellas Felices 🐾
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>

</html>