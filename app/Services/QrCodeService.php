<?php

namespace App\Services;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Writer;

class QrCodeService
{
    public static function svgBase64(?string $content, int $size = 200): string
    {
        if (empty($content)) {
            return '';
        }

        $renderer = new ImageRenderer(
            new RendererStyle($size),
            new SvgImageBackEnd()
        );

        $svg = (new Writer($renderer))->writeString($content);

        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
    public static function pngBase64(string $text, int $size = 200): string 
        { 
            $renderer = new ImageRenderer(
                new RendererStyle(200),
                new ImagickImageBackEnd('png')
            );
        
            $writer = new Writer($renderer);
            $qrCode = $writer->writeString($text);
        
            // Guardar la imagen en un archivo
            file_put_contents('qr.png', $qrCode);
        
            // Devolver la imagen como respuesta
            return response($qrCode)
                ->header('Content-Type', 'image/png');
 
        }    

}
