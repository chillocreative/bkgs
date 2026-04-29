<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\ImageManager;

class LogoProcessor
{
    /**
     * Returns ['original' => path, 'small' => path, 'large' => path]
     * Stored on the 'public' disk under branding/{uuid}/...
     */
    public function process(UploadedFile $file): array
    {
        $manager = new ImageManager(new GdDriver());
        $folder = 'branding/'.Str::uuid()->toString();

        $originalRelative = $file->storeAs(
            $folder,
            'original.'.($file->getClientOriginalExtension() ?: 'png'),
            'public'
        );

        $disk = Storage::disk('public');
        $absoluteOriginal = $disk->path($originalRelative);

        $smallRelative = $folder.'/logo-256.png';
        $largeRelative = $folder.'/logo-512.png';

        $this->makeSquare($manager, $absoluteOriginal, $disk->path($smallRelative), 256);
        $this->makeSquare($manager, $absoluteOriginal, $disk->path($largeRelative), 512);

        return [
            'original' => $originalRelative,
            'small' => $smallRelative,
            'large' => $largeRelative,
        ];
    }

    private function makeSquare(ImageManager $manager, string $sourcePath, string $destPath, int $size): void
    {
        $img = $manager->decodePath($sourcePath);

        $img = $img->scaleDown($size, $size);

        $canvas = $manager->createImage($size, $size);
        $canvas->fill('#ffffff');

        $offsetX = intval(($size - $img->width()) / 2);
        $offsetY = intval(($size - $img->height()) / 2);
        $canvas->insert($img, $offsetX, $offsetY, 'top-left');

        $dir = dirname($destPath);
        if (! is_dir($dir)) @mkdir($dir, 0755, true);

        $encoded = $canvas->encodeUsingFileExtension('png');
        $encoded->save($destPath);
    }
}
