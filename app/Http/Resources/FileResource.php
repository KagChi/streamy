<?php

namespace App\Http\Resources;

use App\Models\TelegramFile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

        $file = TelegramFile::where('name', $this->resource)
            ->firstOrFail();
        
        return [
            'id' => $file->id,
            'url' => url("files/{$this->resource}"),
            'size' => $file->size,
            'mime_type' => $file->mime_type
        ];
    }
}
