<?php

declare(strict_types=1);

namespace App\Contracts;

use Illuminate\Http\UploadedFile;

interface MediaStorage
{
    /**
     * Store the uploaded file and return the canonical path (without any driver prefix).
     * Callers persist this path verbatim; url() resolves it back to a full URL.
     */
    public function put(UploadedFile $file, string $directory): string;

    /**
     * Absolute URL to fetch the file (local dev URL or CDN URL depending on driver).
     */
    public function url(string $path): string;
}
