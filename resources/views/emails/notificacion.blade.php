<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Notificación Digital — LimpiaTuRincón</title>
</head>

<body style="margin:0; padding:0; background-color:#f2f7f3; font-family:Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f2f7f3; padding:20px 0;">
        <tr>
            <td align="center">

                <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:10px; padding:20px; border: 1px solid #e1e8e3;">

                    <!-- Icono Alerta / Notificación -->
                    <tr>
                        <td align="center" style="padding-bottom:15px;">
                            <div style="background-color:#fff3cd; border-radius:12px; width:80px; height:80px; display:inline-block; vertical-align:middle; text-align:center;">
                                <!-- Icono de documento/alerta limpio en SVG -->
                                <svg width="50" height="80" viewBox="0 0 24 24" fill="none" style="margin:auto;">
                                    <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z" fill="#d97706" />
                                </svg>
                            </div>
                        </td>
                    </tr>

                    <!-- Encabezado Principal -->
                    <tr>
                        <td align="center" style="font-size:22px; font-weight:bold; color:#1b4332; padding-bottom:10px;">
                            Notificación de Contravencion a la ordenanza 332 del DMQ
                            <br>
                            <span style="color:#d97706; font-size:16px;">Código: {{ $notificacion->codigo_envio ?? 'N/A' }}</span>
                        </td>
                    </tr>

                    <!-- Cuerpo del Mensaje -->
                    <tr>
                        <td style="font-size:15px; color:#444; line-height:1.6; padding:0 10px 20px 10px;">
                            Estimado/a <strong>{{ $notificacion->contribuyente_nombre }}</strong> (ID: {{ $notificacion->contribuyente_identificacion }}),
                            <br><br>
                            Se ha generado una notificación formal en la plataforma <strong>LimpiaTuRincón</strong> vinculada a su predio ubicado en la dirección:
                            <br>
                            <strong style="color: #1b4332;">{{ $notificacion->contribuyente_direccion }} (Nº Predio: {{ $notificacion->numero_predio }})</strong>.
                            <br><br>
                            A continuación, se detallan los motivos del requerimiento:
                        </td>
                    </tr>

                    <!-- Tabla de Detalles del Requerimiento -->
                    <tr>
                        <td style="padding: 0 10px 20px 10px;">
                            <table width="100%" cellpadding="10" cellspacing="0" style="border: 1px solid #e2e8f0; border-radius: 8px; font-size: 14px;">
                                <tr style="background-color: #f8fafc;">
                                    <td width="35%"><strong>Barrio / Sector:</strong></td>
                                    <td>{{ $notificacion->barrio->nombre ?? 'No especificado' }}</td>
                                </tr>
                                @if($notificacion->ordenanza332)
                                <tr>
                                    <td><strong>Infracción / Ordenanza:</strong></td>
                                    <td>
                                        <strong>{{ $notificacion->ordenanza332->codigo ?? 'Ord. 332' }}</strong>:
                                        {{ $notificacion->ordenanza332->descripcion ?? 'Incumplimiento de normativa de limpieza.' }}
                                    </td>
                                </tr>
                                @endif
                                <tr style="background-color: #f8fafc;">
                                    <td><strong>Nº Denuncia Asociada:</strong></td>
                                    <td>{{ $notificacion->denuncia->codigo ?? $notificacion->denuncia_id ?? 'Inspección de Oficio' }}</td>
                                </tr>
                                @if($notificacion->observacion)
                                <tr>
                                    <td><strong>Observaciones adicionales:</strong></td>
                                    <td><em>{{ $notificacion->observacion }}</em></td>
                                </tr>
                                @endif
                            </table>
                        </td>
                    </tr>

                    <!-- Caja de Vencimiento y Plazos -->
                    <tr>
                        <td style="font-size:14px; color:#78350f; padding:15px; background-color:#fef3c7; border-radius:8px; border-left: 5px solid #d97706;">
                            <strong>Plazo Perentorio:</strong> Cuenta con un lapso de <strong>{{ $notificacion->plazo_horas }} horas</strong> para subsanar la situación descrita a partir de la fecha de notificación.
                            <br><br>
                            <strong>Fecha de Emisión:</strong> {{ $notificacion->fecha_notificacion ? $notificacion->fecha_notificacion->format('d/m/Y H:i') : now()->format('d/m/Y H:i') }}
                            <br>
                            <strong>Fecha de Vencimiento:</strong> {{ $notificacion->fecha_vencimiento ? $notificacion->fecha_vencimiento->format('d/m/Y H:i') : 'Establecida por normativa' }}
                        </td>
                    </tr>

                    <!-- Nota de Advertencia -->
                    <tr>
                        <td style="font-size:12px; color:#6c757d; padding: 20px 10px 0 10px; line-height: 1.4;">
                            * El incumplimiento de los plazos aquí otorgados dará lugar a las sanciones y multas estipuladas en la legislación vigente. Si ya ha resuelto este inconveniente, puede verificar el estado ingresando a su perfil de vecino o contactando al inspector asignado.
                        </td>
                    </tr>

                    <!-- Pie de página -->
                    <tr>
                        <td align="center" style="padding-top:25px; font-size:12px; color:#999;">
                            © {{ date('Y') }} LimpiaTuRincón — Gestión Barrial Inteligente
                            <br>
                            <span style="font-size: 10px; color: #bbb;">Este mensaje fue enviado vía {{ $notificacion->medioLabel() }} de manera automática.</span>
                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</body>

</html>