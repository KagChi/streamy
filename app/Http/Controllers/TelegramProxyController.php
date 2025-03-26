<?php

namespace App\Http\Controllers;

use App\Models\TelegramFile;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TelegramProxyController extends Controller
{
    public function fetchFile($file)
    {
        $telegramFile = TelegramFile::where('name', $file)->first();

        if (!$telegramFile || !$telegramFile->path) {
            return response()->json(['error' => 'File not found'], 404);
        }

        $fileUrl = "https://api.telegram.org/file/bot" . env('TELEGRAM_BOT_TOKEN') . "/{$telegramFile->path}";

        return new StreamedResponse(function () use ($fileUrl) {
            $stream = fopen($fileUrl, 'r');
            fpassthru($stream);
            fclose($stream);
        }, 200, [
            'Content-Type' => $telegramFile->mime_type ?? 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="' . $telegramFile->name . '"',
        ]);
    }
}

