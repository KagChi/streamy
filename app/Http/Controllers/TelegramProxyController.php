<?php

namespace App\Http\Controllers;

use App\Models\TelegramFile;
use Illuminate\Support\Facades\Storage;

class TelegramProxyController extends Controller
{
    public function fetchFile($file)
    {
        $telegramFile = TelegramFile::where('name', $file)->first();
        if (!$telegramFile || !$telegramFile->path) {
            return response()->json(['error' => 'File not found'], 404);
        }

        $fileSize = Storage::disk('telegram')->size($file);

        return response()->stream(function () use ($file, $fileSize) {
            echo Storage::disk("telegram")
                ->get($file);
        }, 200, [
            'Content-Length' => $fileSize,
            'Content-Type' => $telegramFile->mime_type ?? 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="' . $telegramFile->name . '"',
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }
}
