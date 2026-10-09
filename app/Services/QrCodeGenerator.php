<?php

namespace App\Services;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

class QrCodeGenerator
{
    /**
     * Buat QR code SVG (latar putih, modul gelap) agar tetap mudah dipindai saat dicetak.
     */
    public static function svg(string $content, int $size = 512): string
    {
        $style = new RendererStyle(
            $size,
            2,
            null,
            null,
            Fill::uniformColor(new Rgb(255, 255, 255), new Rgb(15, 23, 42)),
        );

        $writer = new Writer(new ImageRenderer($style, new SvgImageBackEnd));

        return $writer->writeString($content, Encoder::DEFAULT_BYTE_MODE_ENCODING, ErrorCorrectionLevel::M());
    }
}
