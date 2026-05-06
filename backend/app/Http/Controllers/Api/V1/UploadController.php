<?php

namespace App\Http\Controllers\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends BaseController
{
    /**
     * Upload d'une image vers MinIO.
     */
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $file = $request->file('file');
        $folder = $request->get('folder', 'covers'); // covers ou avatars
        $filename = $folder . '/' . Str::uuid() . '.' . $file->getClientOriginalExtension();

        $path = Storage::disk('s3')->putFileAs('', $file, $filename, 'public');

        $url = Storage::disk('s3')->url($filename);

        return $this->success([
            'url' => $url,
            'path' => $filename,
        ], 'Image uploadée avec succès', 201);
    }
}