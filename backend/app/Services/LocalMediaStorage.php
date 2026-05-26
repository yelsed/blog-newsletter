<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\MediaStorage;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class LocalMediaStorage implements MediaStorage
{
    private const PREFIX = 'media';

    public function __construct(private readonly Filesystem $disk) {}

    public function put(UploadedFile $file, string $directory): string
    {
        $filename = $this->generateFilename($file);
        $directory = trim($directory, '/');
        $relativePath = $directory.'/'.$filename;

        $this->disk->putFileAs(
            self::PREFIX.'/'.$directory,
            $file,
            $filename,
        );

        return $relativePath;
    }

    public function url(string $path): string
    {
        return $this->disk->url(self::PREFIX.'/'.ltrim($path, '/'));
    }

    private function generateFilename(UploadedFile $file): string
    {
        $original = (string) $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $base = pathinfo($original, PATHINFO_FILENAME);
        $slug = Str::slug($base) ?: 'file';

        return $slug.'-'.Str::ulid()->toBase32().($extension !== '' ? '.'.$extension : '');
    }
}
