<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use League\Flysystem\Filesystem;
use Illuminate\Filesystem\FilesystemAdapter;
use App\Filesystem\TelegramFilesystemAdapter;

class TelegramFilesystemServiceProvider extends ServiceProvider
{
    public function boot()
    {
        \Storage::extend('telegram', function ($app, $config) {
            $chatId = $config['chat_id'] ?? env('TELEGRAM_CHAT_ID');

            $adapter = new TelegramFilesystemAdapter($chatId);
            $filesystem = new Filesystem($adapter);

            return new FilesystemAdapter($filesystem, $adapter, $config);
        });
    }
}
