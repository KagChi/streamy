<?php

namespace App\Filesystem;

use App\Models\TelegramFile;
use Exception;
use League\Flysystem\UnableToSetVisibility;
use RuntimeException;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\FileUpload\InputFile;
use League\Flysystem\Config;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\FileAttributes;

class TelegramFilesystemAdapter implements FilesystemAdapter
{
    protected string $chatId;

    public function __construct(string $chatId)
    {
        $this->chatId = $chatId;
    }

    public function write(string $path, string $contents, Config $config): void
    {
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $contents);
        rewind($stream);

        $response = Telegram::sendDocument([
            'chat_id' => $this->chatId,
            'document' => new InputFile($stream, $path)
        ]);

        fclose($stream);

        if (isset($response['document']) || isset($response['audio'])) {
            $telegramId = $response['document']['file_id'] ?? ($response['audio']['file_id'] ?? null);
            $fileSize = $response['document']['file_size'] ?? $response['audio']['file_size'] ?? null;
            $mimeType = $response['document']['mime_type'] ?? $response['audio']['mime_type'] ?? null;

            $fileResponse = Telegram::getFile(['file_id' => $telegramId]);
            $path = $fileResponse['file_path'] ?? null;

            TelegramFile::create([
                'name' => $path,
                'telegram_id' => $telegramId,
                'path' => $path,
                'size' => $fileSize,
                'mime_type' => $mimeType,
                'chat_id' => $this->chatId,
            ]);
        }
    }

    public function writeStream(string $path, $contents, Config $config): void
    {
        $response = Telegram::sendDocument([
            'chat_id' => $this->chatId,
            'document' => new InputFile($contents, $path)
        ]);

        if (isset($response['document']) || isset($response['audio'])) {
            $telegramId = $response['document']['file_id'] ?? ($response['audio']['file_id'] ?? null);
            $fileSize = $response['document']['file_size'] ?? $response['audio']['file_size'] ?? null;
            $mimeType = $response['document']['mime_type'] ?? $response['audio']['mime_type'] ?? null;
    
            $fileResponse = Telegram::getFile(['file_id' => $telegramId]);
            $filePath = $fileResponse['file_path'] ?? null;
    
            TelegramFile::create([
                'name' => $path,
                'telegram_id' => $telegramId,
                'path' => $filePath,
                'size' => $fileSize,
                'mime_type' => $mimeType,
                'chat_id' => $this->chatId,
            ]);
        }
    }

    public function read(string $path): string
    {
        $file = TelegramFile::where('name', $path)->first();

        if (!$file || !$file->path) {
            throw new RuntimeException("File not found in the database.");
        }

        $fileUrl = "https://api.telegram.org/file/bot" . env('TELEGRAM_BOT_TOKEN') . "/{$file->path}";

        $contents = file_get_contents($fileUrl);

        if ($contents === false) {
            throw new RuntimeException("Failed to download file from Telegram.");
        }

        return $contents;
    }


    public function readStream(string $path)
    {
        $contents = $this->read($path);
        $stream = fopen('php://temp', 'r+');
        fwrite($stream, $contents);
        rewind($stream);

        return $stream;
    }

    public function delete(string $path): void
    {
        $file = TelegramFile::where('name', $path)->first();

        if (!$file) {
            return;
        }

        $file->delete();
    }

    public function getUrl(string $path): string
    {
        $file = TelegramFile::where('name', $path)->first();

        if (!$file || !$file->path) {
            throw new RuntimeException("File not found in the database.");
        }

        return url("/proxy/telegram/{$path}");
    }


    public function deleteDirectory(string $path): void {}
    public function createDirectory(string $path, Config $config): void {}
    public function setVisibility(string $path, string $visibility): void {
        throw UnableToSetVisibility::atLocation($path, 'Adapter does not support visibility controls.');
    }
    public function visibility(string $path): FileAttributes {
        return new FileAttributes($path);
    }
    public function fileExists(string $path): bool {
        try {
            $this->read($path);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
    public function directoryExists(string $path): bool { return false; }
    public function listContents(string $path, bool $deep): iterable { return []; }
    public function move(string $source, string $destination, Config $config): void {}
    public function copy(string $source, string $destination, Config $config): void {}
    public function fileSize(string $path): FileAttributes
    {
        $file = TelegramFile::where('name', $path)->first();

        if (!$file || !$file->size) {
            throw new RuntimeException("File size not available.");
        }

        return new FileAttributes($path, $file->size, null, null, $file->mime_type);
    }

    public function mimeType(string $path): FileAttributes
    {
        $file = TelegramFile::where('name', $path)->first();

        if (!$file || !$file->mime_type) {
            throw new RuntimeException("MIME type not available.");
        }

        return new FileAttributes($path, null, null, null, $file->mime_type);
    }
    public function lastModified(string $path): FileAttributes
    {
        $file = TelegramFile::where('name', $path)->first();

        if (!$file || !$file->updated_at) {
            throw new RuntimeException("Last modified timestamp not available.");
        }

        return new FileAttributes($path, null, null, $file->updated_at->timestamp);
    }
}
