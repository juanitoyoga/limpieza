<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: DejaVu Sans;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .title {
            font-size: 16px;
            font-weight: bold;
        }

        .section {
            margin-bottom: 8px;
        }

        .label {
            font-weight: bold;
        }

        .qr {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 4px;
            vertical-align: top;
        }

        .footer {
            margin-top: 20px;
            font-size: 10px;
        }
    </style>
</head>

<body>




    <div class="header">
        <div class="title">CONSTANCIA ADMINISTRATIVA DE NOTIFICACIÓN</div>
        <div>{{ $institucion }}</div>
    </div>


    <div style="text-align: center; margin-top: 25px;">

        <img
            src="{{ \App\Services\QrCodeService::svgBase64(
            route('descargar-documento', $nomination->document_path)
        ) }}"
            alt="Código QR de verificación"
            style="
            width: 120px;
            height: 120px;
            margin: 0 auto;
            display: block;
        ">

        <p style="
        margin-top: 10px;
        font-size: 10px;
        color: #555;
        font-style: italic;
    ">
            Código QR para verificación de la autenticidad de esta nominación.
            <br>
            Escanéelo para consultar el registro oficial en línea.
        </p>

    </div>

    <table>
        <tr>
            <td class="label"><span><strong>DATOS DE CONTROL</strong></span></td>
        </tr>
        <tr>
            <td class="label">Generado por:</td>
            <td>{{ $responsable }}</td>
        </tr>
        <tr>
            <td class="label">Verificado por:</td>
            <td>{{ $verificado_por }}</td>
        </tr>
        <tr>
            <td class="label">Fecha Verificacion:</td>
            <td>{{ $nomination->verified_at?->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td class="label">Rechazada por:</td>
            <td>{{ $rechazado_por }}</td>
        </tr>
        <tr>
            <td class="label">Fecha Rechazo:</td>
            <td>{{ $nomination->rejected_at?->format('d/m/Y H:i') }}</td>
        </tr>

        <tr>
            <td class="label"><span><strong>DATOS DE LA NOMINACION</strong></span></td>
        </tr>
        <tr>
            <td class="label">Número Trámite:</td>
            <td>{{ $numero_tramite }}</td>
        </tr>

        <tr>
            <td class="label">Candidato:</td>
            <td>{{ $candidato }}</td>
        </tr>
        <tr>
            <td class="label">Nominado a:</td>
            <td>{{ $posicion }}</td>
        </tr>
        <tr>
            <td class="label">Fecha ingreso al sistema:</td>
            <td>{{ $nomination->created_at?->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td class="label">Estado:</td>
            <td>{{ $nomination->estado }}</td>
        </tr>
        <tr>
            <td class="label">Fecha de emisión:</td>
            <td>{{ $nomination->fecha_emision?->format('d/m/Y H:i')  }}</td>
        </tr>

        <tr>
            <td class="label">Inicio de funciones:</td>
            <td>{{ $nomination->fecha_inicio_vigencia?->format('d/m/Y H:i')  }}</td>
        </tr>
        <tr>
            <td class="label">Fin de funciones:</td>
            <td>{{ $nomination->fecha_fin_vigencia?->format('d/m/Y H:i')  }}</td>
        </tr>
        <tr>
            <td class="label">Observaciones:</td>
            <td class="pb-4">{{ $observaciones }}</td> <!-- pb-4 = padding-bottom 1rem -->
        </tr>

        <tr>
            <td class="label pt-2">Firma Responsable:</td> <!-- pt-2 = padding-top 0.5rem -->
            <td class="pt-2">__________________________________________</td>
        </tr>
    </table>

    <div class="footer">
        Documento generado electrónicamente.<br>
        Código de verificación: {{ $hash }}<br>
        @if(isset($urlVerificacion) && $urlVerificacion)
        Verificar en: {{ $nomination->document_path }}
        @endif
    </div>

</body>

</html>