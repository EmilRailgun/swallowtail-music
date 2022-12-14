<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\Artist;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ArtistController extends Controller
{
    //

    public function getAll()
    {
        $listArtist = Artist::all();
        return response()->json($listArtist, Response::HTTP_OK);
    }

    public function show($id)
    {
        $artist = DB::table("artists")
            ->leftJoin(
                "song_artists",
                "artists.artist_id",
                "=",
                "song_artists.artist_id"
            )
            ->leftJoin("songs", "song_artists.song_id", "=", "songs.song_id")
            ->select(
                "artists.*",
                DB::raw("sum(songs.listens) as total_listens"),
                DB::raw("count(distinct songs.album_id) as total_album")
            )
            ->where("artists.artist_id", $id)
            ->groupBy("artists.artist_id")
            ->first();
        return response()->json(
            [
                "status" => "success",
                "artist" => $artist,
            ],
            Response::HTTP_OK
        );
    }

    public function searchArtist(Request $request)
    {
        if ($request->query->has("query")) {
            $query = $request->query->get("query");
            $artists = Artist::where("name", "like", "%" . $query . "%")->get();
            return response()->json([
                "status" => "success",
                "artists" => $artists,
            ]);
        } else {
            return response()->json([
                "status" => "error",
                "message" => "Query not found",
            ]);
        }
    }

    public function getTop()
    {
        $artist = DB::table("artists")
            ->leftJoin(
                "song_artists",
                "artists.artist_id",
                "=",
                "song_artists.artist_id"
            )
            ->leftJoin("songs", "song_artists.song_id", "=", "songs.song_id")
            ->select("artists.*", DB::raw("sum(songs.listens) as total"))
            ->groupBy("artists.artist_id")
            ->limit(8)
            ->get();
        return response()->json([
            "status" => "success",
            "artists" => $artist,
        ]);
    }

    public function getBySongByArtistId($id)
    {
        $artist = DB::table("artists")
            ->join(
                "song_artists",
                "artists.artist_id",
                "=",
                "song_artists.artist_id"
            )
            ->join("songs", "song_artists.song_id", "=", "songs.song_id")
            ->select("artists.*")
            ->where("songs.song_id", $id)
            ->get();
        return response()->json([
            "status" => "success",
            "artists" => $artist,
        ]);
    }

    public function getTopSongByArtistId($id)
    {
        $songs = Song::orderBy("listens", "desc")
            ->limit(10)
            ->with([
                "artist",
                "album",
                "like" => function ($query) {
                    $query->where("user_id", Auth::user()->user_id);
                },
            ])
            ->get();

        $songs = $songs->filter(function ($song) use ($id) {
            return array_filter($song->artist->toArray(), function (
                $artist
            ) use ($id) {
                return $artist["artist_id"] == $id;
            });
        });
        $songs = array_values($songs->toArray());
        return response()->json([
            "status" => "success",
            "songs" => $songs,
        ]);
    }

    public function getAlbumByArtistId($id)
    {
        $albums = DB::table("songs")
            ->join("albums", "songs.album_id", "=", "albums.album_id")
            ->join("song_artists", "songs.song_id", "=", "song_artists.song_id")
            ->join(
                "artists",
                "song_artists.artist_id",
                "=",
                "artists.artist_id"
            )
            ->groupBy("albums.album_id")
            ->select(
                "albums.*",
                DB::raw("count(albums.album_id) as song_count")
            )
            ->where("artists.artist_id", $id)
            ->get();
        return response()->json([
            "status" => "success",
            "albums" => $albums,
        ]);
    }
}
