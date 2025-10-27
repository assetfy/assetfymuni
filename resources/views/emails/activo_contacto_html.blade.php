<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>{{ $asunto }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body style="margin:0;padding:0;background:#f6f7f9;">
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background:#f6f7f9;">
        <tr>
            <td align="center" style="padding:24px;">
                <table role="presentation" cellpadding="0" cellspacing="0" width="100%"
                    style="max-width:640px;background:#ffffff;border-radius:8px;overflow:hidden;border:1px solid #e5e7eb;">
                    <tr>
                        <td style="padding:20px 24px;border-bottom:1px solid #e5e7eb;">
                            <h3
                                style="margin:0;font-family:Arial,Helvetica,sans-serif;font-size:20px;line-height:1.3;color:#111827;">
                                {{ $asunto }}
                            </h3>
                        </td>
                    </tr>

                    <tr>
                        <td
                            style="padding:20px 24px;font-family:Arial,Helvetica,sans-serif;color:#111827;font-size:14px;line-height:1.6;">
                            <p style="margin:0 0 12px 0;">
                                <strong>Mensaje:</strong><br>
                                {!! nl2br(e($mensaje)) !!}
                            </p>

                            <hr style="border:0;border-top:1px solid #e5e7eb;margin:16px 0;">

                            <p style="margin:0 0 6px 0;"><strong>Empresa:</strong> {{ $empresa }}</p>
                            <p style="margin:0 0 6px 0;"><strong>Remitente:</strong> {{ $remitenteNombre }}
                                &lt;{{ $remitenteEmail }}&gt;</p>
                            <p style="margin:0 0 6px 0;"><strong>Bien:</strong> {{ $bienNombre }}</p>
                            <p style="margin:0 0 0 0;">
                                <strong>Ver bien:</strong>
                                <a href="{{ $bienUrl }}"
                                    style="color:#2563eb;text-decoration:none;word-break:break-all;" target="_blank"
                                    rel="noopener">
                                    {{ $bienUrl }}
                                </a>
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:14px 24px;border-top:1px solid #e5e7eb;background:#fafafa;">
                            <p style="margin:0;font-family:Arial,Helvetica,sans-serif;color:#6b7280;font-size:12px;">
                                Enviado por Assetfy
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>

</html>
