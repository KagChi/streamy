<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArtistTrack extends Model
{
    use HasFactory;

    protected $fillable = [
        'artist_id',
        'track_id',
    ];

    public function artist()
    {
        return $this->belongsTo(Artist::class);
    }

    public function track()
    {
        return $this->belongsTo(Track::class);
    }
}
