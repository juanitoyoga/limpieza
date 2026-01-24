<?php

namespace App\Services;

use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
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
}
