<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('telegram_files', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('telegram_id');
            $table->string('path')->nullable();
            $table->unsignedBigInteger('size')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('chat_id');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('telegram_files');
    }
};
