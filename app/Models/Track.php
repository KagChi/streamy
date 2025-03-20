<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Track extends Model
{
    protected $fillable = [
        'title',
        'cover',
        'isrc',
        'duration',
    ];

    public function artists()
    {
        return $this->belongsToMany(Artist::class, 'artist_tracks');
    }
}
