<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'telegram_id',
        'path',
        'chat_id',
        'size',
        'mime_type',
    ];
}

