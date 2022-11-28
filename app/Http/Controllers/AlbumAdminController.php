<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\Album;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AlbumAdminController extends Controller
{
    //
    public function getAll()
    {
        if (Auth::user()->role === '') {
            return response()->json(['message' => 'You are not authorized to access this resource.'], 403);
        } else {
            $albums = Album::with(["user"])
                ->withCount(["song"])
                ->get();
            return response()->json([
                'albums' => $albums,
                'status' => "success"
            ], 200);
        }
    }

    public function show($id)
    {
        if (Auth::user()->role === '') {
            return response()->json(['message' => 'You are not authorized to access this resource.'], 403);
        } else {
            $album = Album::with(["user"])
                ->withCount(["song"])
                ->find($id);
            $songs = Song::where('album_id', $id)->get();
            return response()->json([
                'status' => 'success',
                'album' => $album,
                'songs' => $songs,
            ]);
        }
    }

    public function songRemove(Request $request)
    {
        if (Auth::user()->role === '') {
            return response()->json(['message' => 'You are not authorized to do this action.'], 403);
        } else {
            $song = Song::where('song_id', $request->song_id)->first();
            $song->album_id = null;
            $song->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Song removed successfully!'
            ]);
        }
    }
}
