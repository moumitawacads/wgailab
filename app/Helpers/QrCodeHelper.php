<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeHelper
{
    /**
     * Generate and save QR Code
     *
     * @param string $content
     * @param string $fileName
     * @param string $directory
     * @param int $size
     * @return string
     */
    public static function generate(
        string $content,
        string $fileName,
        string $directory = 'qrcodes',
        int $size = 300
    ): string {

        $qrCode = QrCode::format('png')
            ->size($size)
            ->generate($content);

        $path = "{$directory}/{$fileName}.png";

        Storage::disk('public')->put($path, $qrCode);

        return Storage::url($path);
    }
}