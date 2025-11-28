<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido a LimpiaTuRincón</title>
</head>
<body style="margin:0; padding:0; background-color:#f2f7f3; font-family:Arial, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f2f7f3; padding:20px 0;">
    <tr>
        <td align="center">

            <table width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:10px; padding:20px;">
                
                <tr>
                    <td align="center" style="padding-bottom:15px;">
                        <div style="background-color:#e8f5e9; border-radius:12px; width:80px; height:80px; display:flex; align-items:center; justify-content:center;">
                            <svg width="50" height="50" viewBox="0 0 24 24" fill="none">
                                <path d="M4 20c0-4 4-8 8-8s8 4 8 8" stroke="#2f9e44" stroke-width="1.6" stroke-linecap="round"/>
                                <path d="M12 4c2 2 3 2 5 0" stroke="#2f9e44" stroke-width="1.6" stroke-linecap="round"/>
                                <path d="M7.5 12.5l2 2 4-4" stroke="#2f9e44" stroke-width="1.6" stroke-linecap="round"/>
                            </svg>
                        </div>
                    </td>
                </tr>

                <tr>
                    <td align="center" style="font-size:22px; font-weight:bold; color:#1b4332; padding-bottom:10px;">
                        ¡Bienvenido, {{ $user->first_name ?? 'vecino' }}!  
                        <br>
                        <span style="color:#2f9e44;">LimpiaTuRincón</span> te da la bienvenida 🧹
                    </td>
                </tr>

                <tr>
                    <td style="font-size:15px; color:#444; line-height:1.6; padding:0 10px 20px 10px;">
                        Gracias por unirte a la plataforma de gestión barrial <strong>LimpiaTuRincón</strong>.
                        <br><br>
                        Con tu participación podremos mantener nuestro barrio más limpio, seguro y ordenado.
                        <br><br>
                        <strong>Desde hoy puedes:</strong>
                        <ul style="margin:10px 0 0 20px; padding:0; color:#555;">
                            <li>Reportar puntos sucios con fotos y ubicación.</li>
                            <li>Ver el estado de limpieza del barrio.</li>
                            <li>Participar en jornadas comunitarias de aseo.</li>
                        </ul>
                    </td>
                </tr>

                <tr>
                    <td style="font-size:14px; color:#6c757d; padding:10px 20px; background-color:#f8faf8; border-radius:8px;">
                        <strong>Recuerda:</strong> Mantener la ciudad limpia es un deber ciudadano.
                        Cada acción cuenta. 🌱
                    </td>
                </tr>

                <tr>
                    <td align="center" style="padding-top:25px; font-size:12px; color:#999;">
                        © {{ date('Y') }} LimpiaTuRincón — Gestión Barrial Inteligente
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>

