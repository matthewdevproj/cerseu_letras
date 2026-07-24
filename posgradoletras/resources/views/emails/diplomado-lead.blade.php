<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Nueva solicitud de información - Diplomados</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2937; background: #f9fafb; padding: 24px;">
    <div style="max-width: 560px; margin: 0 auto; background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb;">
        <div style="background: #761e23; color: #ffffff; padding: 20px 24px;">
            <h1 style="margin: 0; font-size: 18px;">Nueva solicitud de información — Diplomados</h1>
        </div>
        <div style="padding: 24px;">
            <p style="margin: 0 0 16px;">Se ha registrado una nueva solicitud de información a través del portal de Diplomados de la Unidad de Posgrado.</p>

            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <tr>
                    <td style="padding: 8px 0; color: #6b7280; width: 160px;">Diplomado de interés</td>
                    <td style="padding: 8px 0; font-weight: bold;">{{ $lead->programa?->titulo_completo ?? 'No especificado' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #6b7280;">Nombres</td>
                    <td style="padding: 8px 0;">{{ $lead->nombres }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #6b7280;">Apellidos</td>
                    <td style="padding: 8px 0;">{{ $lead->apellidos }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #6b7280;">Correo electrónico</td>
                    <td style="padding: 8px 0;"><a href="mailto:{{ $lead->correo }}">{{ $lead->correo }}</a></td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #6b7280;">Teléfono</td>
                    <td style="padding: 8px 0;">{{ $lead->telefono }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #6b7280;">País</td>
                    <td style="padding: 8px 0;">{{ $lead->pais }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; color: #6b7280;">Región / Departamento</td>
                    <td style="padding: 8px 0;">{{ $lead->region }}</td>
                </tr>
            </table>

            <p style="margin: 24px 0 0; font-size: 12px; color: #9ca3af;">
                Este correo fue generado automáticamente desde el formulario de solicitud de información de la página de Diplomados.
            </p>
        </div>
    </div>
</body>
</html>
