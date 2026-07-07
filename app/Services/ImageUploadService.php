<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Format;
use Intervention\Image\Laravel\Facades\Image;

class ImageUploadService
{
    public function store(UploadedFile $file, string $directory, int $width = 1024, int $height = 1280, int $quality = 80): string
    {
        $image = Image::decode($file)->cover($width, $height, \Intervention\Image\Alignment::TOP);

        $filename = Str::uuid() . '.webp';
        $path = trim($directory, '/') . '/' . $filename;

        $encoded = $image->encodeUsingFormat(Format::WEBP, quality: $quality);

        Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }

    public function storeFromUrl(string $url, string $directory, int $width = 1024, int $height = 1280, int $quality = 80): ?string
    {
        $response = \Illuminate\Support\Facades\Http::timeout(30)->get($url);

        if ($response->failed()) {
            return null;
        }

        $image = \Intervention\Image\Laravel\Facades\Image::decode($response->body())
            ->cover($width, $height, \Intervention\Image\Alignment::TOP);

        $filename = \Illuminate\Support\Str::uuid() . '.webp';
        $path = trim($directory, '/') . '/' . $filename;

        $encoded = $image->encodeUsingFormat(\Intervention\Image\Format::WEBP, quality: $quality);

        \Illuminate\Support\Facades\Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }

    public function storeFromBinary(string $binaryData, string $directory, int $width = 1024, int $height = 1280, int $quality = 80): string
    {
        $image = \Intervention\Image\Laravel\Facades\Image::decode($binaryData)
            ->cover($width, $height, \Intervention\Image\Alignment::TOP);

        $filename = \Illuminate\Support\Str::uuid() . '.webp';
        $path = trim($directory, '/') . '/' . $filename;

        $encoded = $image->encodeUsingFormat(\Intervention\Image\Format::WEBP, quality: $quality);

        \Illuminate\Support\Facades\Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }

    public function storeTransparent(\Illuminate\Http\UploadedFile $file, string $directory, int $quality = 90): string
    {
        $image = \Intervention\Image\Laravel\Facades\Image::decode($file);

        $filename = \Illuminate\Support\Str::uuid() . '.webp';
        $path = trim($directory, '/') . '/' . $filename;

        $encoded = $image->encodeUsingFormat(\Intervention\Image\Format::WEBP, quality: $quality);

        \Illuminate\Support\Facades\Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }

    public function storeLogo(\Illuminate\Http\UploadedFile $file, string $directory): string
    {
        $image = \Intervention\Image\Laravel\Facades\Image::decode($file);

        $filename = \Illuminate\Support\Str::uuid() . '.png';
        $path = trim($directory, '/') . '/' . $filename;

        $encoded = $image->encodeUsingFormat(\Intervention\Image\Format::PNG);

        \Illuminate\Support\Facades\Storage::disk('public')->put($path, (string) $encoded);

        return $path;
    }
}