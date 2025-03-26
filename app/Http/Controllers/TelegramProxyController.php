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
        
        $headers = get_headers($fileUrl, 1);
        if (!strpos($headers[0], "200")) {
            return response()->json(['error' => 'File not accessible'], 404);
        }

        $contentLength = $headers['Content-Length'] ?? null;

        $responseHeaders = [
            'Content-Type' => $telegramFile->mime_type ?? 'application/octet-stream',
            'Content-Disposition' => 'inline; filename="' . $telegramFile->name . '"',
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ];

        if ($contentLength) {
            $responseHeaders['Content-Length'] = $contentLength;
        }

        return new StreamedResponse(function () use ($fileUrl) {
            $stream = fopen($fileUrl, 'r');
            if ($stream) {
                fpassthru($stream);
                fclose($stream);
            }
        }, 200, $responseHeaders);
    }
}

