<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Admin\Media;

use App\Contracts\MediaStorage;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Media\UploadMediaRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function __invoke(UploadMediaRequest $request, MediaStorage $storage): JsonResponse
    {
        /** @var UploadedFile $file */
        $file = $request->file('file');

        $emailId = $request->integer('emailId');
        $directory = $emailId > 0
            ? 'emails/'.$emailId
            : 'uploads/'.Str::ulid()->toBase32();

        $path = $storage->put($file, $directory);

        return response()->json([
            'path' => $path,
            'url' => $storage->url($path),
        ], 201);
    }
}
