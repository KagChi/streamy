<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class TrackResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'isrc' => $this->isrc,
            'cover' => Storage::disk("telegram")
                ->url($this->cover),
            'artists' => ArtistResource::collection($this->whenLoaded('artists')),
            'files' => $this->when(
                $request->routeIs('tracks.show'),
                fn() => FileResource::collection($this->files)
            ),
        ];
    }
}
