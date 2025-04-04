<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Artist extends Model
{
    protected $fillable = [
        'name',
        'cover',
    ];

    public function tracks()
    {
        return $this->hasMany(Track::class, 'artist_tracks');
    }
}
