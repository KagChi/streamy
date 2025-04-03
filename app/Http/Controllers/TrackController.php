<?php

namespace App\Http\Controllers;

use App\Http\Resources\TrackResource;
use App\Models\Track;
use Illuminate\Http\Request;

class TrackController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Track::query()
            ->with('artists');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('title', 'like', "%{$search}%")
                ->orWhere('isrc', 'like', "%{$search}%");
        }

        return TrackResource::collection($query->paginate(15));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'isrc' => 'required|string|max:255|unique:tracks',
            'file' => 'required|file|mimes:mp3,wav,flac,aac,ogg|max:102400',
            'artist_ids' => 'required|array',
            'artist_ids.*' => 'exists:artists,id',
            'cover' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:4096',
            'duration' => 'required|integer|min:0',
        ]);

        if ($request->hasFile('cover')) {
            $imagePath = $request->file('cover')->store('covers', 'telegram');
            $validated['cover'] = $imagePath;
        }

        if ($request->hasFile('file')) {
            $audioPath = $request->file('file')->store('tracks', 'telegram');
            $validated['files'] = explode(",", $audioPath);
        }

        $track = Track::create($validated);
        $track->artists()->attach($validated['artist_ids']);
        
        return response('', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Track $track)
    {
        return new TrackResource($track->load(['artists']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Track $track)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Track $track)
    {
        //
    }
}
