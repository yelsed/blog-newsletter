<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\MediaStorage;
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use RuntimeException;

class BunnyMediaStorage implements MediaStorage
{
    public function __construct(
        private readonly HttpClient $http,
        private readonly string $cdnUrl,
        private readonly string $storageZone,
        private readonly string $storageEndpoint,
        private readonly string $apiKey,
    ) {}

    public function put(UploadedFile $file, string $directory): string
    {
        $filename = $this->generateFilename($file);
        $path = trim($directory, '/').'/'.$filename;
        $url = 'https://'.$this->storageEndpoint.'/'.$this->storageZone.'/'.$path;

        $contents = (string) file_get_contents((string) $file->getRealPath());

        $response = $this->http
            ->withHeaders([
                'AccessKey' => $this->apiKey,
                'Content-Type' => 'application/octet-stream',
            ])
            ->withBody($contents, 'application/octet-stream')
            ->put($url);

        if (! $response->successful()) {
            throw new RuntimeException('Bunny upload failed with status '.$response->status());
        }

        return $path;
    }

    public function url(string $path): string
    {
        return rtrim($this->cdnUrl, '/').'/'.ltrim($path, '/');
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
