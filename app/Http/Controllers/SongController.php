<?php

namespace App\Http\Controllers;

use App\Models\Song;
use App\Models\Genre;
use App\Models\Artist;
use App\Models\LikedSong;
use App\Models\SongGenre;
use App\Models\SongArtist;
use Illuminate\Support\Str;
use App\Models\PlaylistSong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use ProtoneMedia\LaravelFFMpeg\Exporters\EncodingException;

class SongController extends Controller
{
    //

    public function uploadSong(Request $request)
    {
        try {
            $artist = json_decode($request->artist);
            $newArtist = json_decode($request->newArtist);
            $genre = json_decode($request->genre);
            $newGenre = json_decode($request->newGenre);

            $song = new Song();
            $song->song_id = "song_" . date("YmdHi") . Str::random(10);
            $song->user_id = Auth::user()->user_id;
            $song->title = $request->songName;
            $song->display = $request->displayMode;
            $song->listens = 0;

            foreach ($artist as $id) {
                $song_artist = new SongArtist();
                $song_artist->song_id = $song->song_id;
                $song_artist->artist_id = $id;
                $song_artist->save();
            }

            foreach ($newArtist as $name) {
                $check = Artist::where("name", $name)->first();
                if ($check) {
                    $song_artist = new SongArtist();
                    $song_artist->song_id = $song->song_id;
                    $song_artist->artist_id = $check->artist_id;
                    $song_artist->save();
                    continue;
                }
                $artist = new Artist();
                $artist->artist_id = "artist_" . Str::random(10);
                $artist->name = $name;
                $artist->image_path = "";
                $artist->save();
                $song_artist = new SongArtist();
                $song_artist->artist_id = $artist->artist_id;
                $song_artist->song_id = $song->song_id;
                $song_artist->save();
            }

            foreach ($genre as $id) {
                $song_genre = new SongGenre();
                $song_genre->song_id = $song->song_id;
                $song_genre->genre_id = $id;
                $song_genre->save();
            }

            foreach ($newGenre as $name) {
                $check = Genre::where('$name', $name)->first();
                if ($check) {
                    $song_genre = new SongGenre();
                    $song_genre->song_id = $song->song_id;
                    $song_genre->genre_id = $check->genre_id;
                    $song_genre->save();
                    continue;
                }
                $genre = new Genre();
                $genre->genre_id = "genre_" . Str::random(10);
                $genre->name = $name;
                $genre->save();
                $song_genre = new SongGenre();
                $song_genre->genre_id = $genre->genre_id;
                $song_genre->song_id = $song->song_id;
                $song_genre->save();
            }
            return json_encode([
                "status" => "success",
                "message" =>
                    "Song uploaded successfully. It will ready in some minutes.",
            ]);
        } finally {
            if ($request->file("songFile")) {
                try {
                    // dd($request->file('songFile'));
                    $file = $request->file("songFile");
                    $filename = date("YmdHi") . $file->getClientOriginalName();
                    $file->move(
                        public_path("storage/upload/song_src"),
                        $filename
                    );
                    FFMpeg::fromDisk("final_audio")
                        ->open($filename)
                        ->export()
                        ->addFilter([
                            "-strict",
                            "-2",
                            "-acodec",
                            "vorbis",
                            "-b:a",
                            "320k",
                        ])
                        ->save($song->song_id . ".ogg");
                    @unlink(
                        public_path("storage/upload/song_src/") . $filename
                    );
                    $duration = FFMpeg::fromDisk("final_audio")
                        ->open($song->song_id . ".ogg")
                        ->getDurationInSeconds();
                    $song->duration = $duration;
                    $song->save();
                    FFMpeg::cleanupTemporaryFiles();
                } catch (EncodingException $exception) {
                    $command = $exception->getCommand();
                    $errorLog = $exception->getErrorOutput();
                    dd($command, $errorLog);
                }
            }
        }
    }

    public function updateSong(Request $request)
    {
        $artist = json_decode($request->artist);
        $newArtist = json_decode($request->newArtist);
        $genre = json_decode($request->genre);
        $newGenre = json_decode($request->newGenre);
        $song = Song::find($request->song_id);
        if ($song->user_id != Auth::user()->user_id) {
            return response()->json([
                "status" => "error",
                "message" => "You are not allowed to update this song.",
            ]);
        }
        $song->title = $request->songName;
        $song->display = $request->displayMode;
        $song->save();
        $song->artist()->detach();
        $song->genre()->detach();
        $song->artist()->attach($artist);
        $song->genre()->attach($genre);
        foreach ($newArtist as $name) {
            $check = Artist::where("name", $name)->first();
            if ($check) {
                $song_artist = new SongArtist();
                $song_artist->song_id = $song->song_id;
                $song_artist->artist_id = $check->artist_id;
                $song_artist->save();
                continue;
            }
            $saveNewArtist = new Artist();
            $saveNewArtist->artist_id = "artist_" . Str::random(10);
            $saveNewArtist->name = $name;
            $saveNewArtist->image_path = "";
            $saveNewArtist->save();
            $song->artist()->attach([$saveNewArtist->artist_id]);
        }
        foreach ($newGenre as $name) {
            $check = Genre::where('$name', $name)->first();
            if ($check) {
                $song_genre = new SongGenre();
                $song_genre->song_id = $song->song_id;
                $song_genre->genre_id = $check->genre_id;
                $song_genre->save();
                continue;
            }
            $savenewGenre = new Genre();
            $savenewGenre->genre_id = "genre_" . Str::random(10);
            $savenewGenre->name = $name;
            $savenewGenre->save();
            $song->genre()->attach([$genre->genre_id]);
        }
        return response()->json([
            "status" => "success",
            "message" => "Song Updated Successfully",
        ]);
    }

    public function uploadedSong()
    {
        $songsData = DB::select(
            'SELECT songs.*,  artists.name AS artist_name,artists.artist_id AS artist_id
                        FROM songs
                        LEFT JOIN song_artists ON songs.song_id = song_artists.song_id
                        LEFT JOIN artists ON song_artists.artist_id =artists.artist_id
                        WHERE songs.user_id = ?',
            [Auth::user()->user_id]
        );

        return response()->json([
            "status" => "success",
            "songs" => $songsData,
        ]);
    }

    public function getSongInfo($id)
    {
        $song = Song::where("song_id", $id)->first();
        if (!$song->id) {
            return response()->json([
                "status" => "error",
                "message" => "Song not found",
            ]);
        }
        if (
            Auth::user()->user_id != $song->user_id &&
            $song->display == "private"
        ) {
            return response()->json(
                [
                    "status" => "error",
                    "message" =>
                        "You are not authorized to access this resource.",
                ],
                403
            );
        }
        $genre = $song
            ->genre()
            ->where("song_id", $id)
            ->get();
        $artist = $song
            ->artist()
            ->where("song_id", $id)
            ->get();
        return response()->json([
            "status" => "success",
            "song" => $song,
            "genre" => $genre,
            "artist" => $artist,
        ]);
    }

    public function getSongForPlay($id)
    {
        $song = Song::where("song_id", $id)
            ->with(["artist", "album", "like"])
            ->first();
        return response()->json([
            "status" => "success",
            "song" => $song,
        ]);
    }

    public function increaseSongListens($id, Request $request)
    {
        $song = Song::find($id);
        $song->listens = $song->listens + 1;
        // Redis::set($request->bearerToken(),  Carbon::now()->timestamp);
        $song->save();
        return response()->json([
            "status" => "success",
            "message" => "Song listens increased",
        ]);
    }

    public function likeSong($id)
    {
        $song = Song::where("song_id", $id)->first();
        if (!$song->id) {
            return response()->json([
                "status" => "error",
                "message" => "Song not found",
            ]);
        }

        $liked = LikedSong::where("song_id", $id)
            ->where("user_id", Auth::user()->user_id)
            ->first();
        if ($liked) {
            $liked->delete();
            return response()->json([
                "status" => "success",
                "message" => "Song unliked successfully",
            ]);
        } else {
            $liked = new LikedSong();
            $liked->song_id = $id;
            $liked->user_id = Auth::user()->user_id;
            $liked->save();
            return response()->json([
                "status" => "success",
                "message" => "Song liked successfully",
            ]);
        }
    }

    public function likedSong($id)
    {
        $song = Song::where("song_id", $id)->first();
        if (!$song->id) {
            return response()->json([
                "status" => "error",
                "message" => "Song not found",
            ]);
        }

        $liked = LikedSong::where("song_id", $id)
            ->where("user_id", Auth::user()->user_id)
            ->first();
        if ($liked) {
            return response()->json([
                "status" => "success",
                "liked" => true,
            ]);
        } else {
            return response()->json([
                "status" => "success",
                "liked" => false,
            ]);
        }
    }

    public function deleteSong($id)
    {
        $song = Song::where("song_id", $id)->first();
        if (!$song->id) {
            return response()->json([
                "status" => "error",
                "message" => "Song not found",
            ]);
        }
        if ($song->user_id != Auth::user()->user_id) {
            return response()->json([
                "status" => "error",
                "message" => "You are not allowed to delete this song!",
            ]);
        }
        SongArtist::where("song_id", $id)->delete();
        SongGenre::where("song_id", $id)->delete();
        PlaylistSong::where("song_id", $id)->delete();
        LikedSong::where("song_id", $id)->delete();
        @unlink(public_path("storage/upload/song_src/") . $id . ".ogg");
        $song->delete();
        return response()->json([
            "status" => "success",
            "message" => "Song deleted successfully",
        ]);
    }

    public function latestSong()
    {
        $songs = Song::with("artist")
            ->where("display", "public")
            ->orderBy("created_at", "DESC")
            ->limit(5)
            ->get();
        return response()->json([
            "status" => "success",
            "songs" => $songs,
        ]);
    }

    public function searchSong(Request $request)
    {
        if ($request->query->has("query")) {
            $query = $request->query->get("query");
            $songs = Song::with(["artist", "album", "like"])
                ->where("songs.title", "like", "%" . $query . "%")
                ->orWhere("songs.sub_title", "like", "%" . $query . "%")
                ->get();
            return response()->json([
                "status" => "success",
                "songs" => $songs,
            ]);
        } else {
            return response()->json([
                "status" => "error",
                "message" => "Query not found",
            ]);
        }
    }
}
