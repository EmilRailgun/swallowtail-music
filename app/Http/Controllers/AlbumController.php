<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Song;
use App\Models\Album;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use ProtoneMedia\LaravelFFMpeg\Exporters\EncodingException;

class AlbumController extends Controller
{
    //

    public function uploadAlbum(Request $request)
    {
        try {
            $album_id = "album_" . Str::random(8) . Carbon::now()->timestamp;
            $name = $request->albumTitle;
            $release_year = $request->albumReleaseYear;
            $type = $request->albumType;
            $songCount = json_decode($request->songCount);
            $album = new Album();
            if ($request->file("albumImage")) {
                $imageFile = $request->file("albumImage");
                $fileName =
                    $album_id . "." . $imageFile->getClientOriginalExtension();
                @unlink(
                    public_path("storage/upload/album_cover") . "/" . $fileName
                );
                $imageFile->move(
                    public_path("storage/upload/album_cover"),
                    $fileName
                );
                $album->image_path = $fileName;
            }

            $album->name = $name;
            $album->album_id = $album_id;
            $album->user_id = Auth::user()->user_id;
            $album->release_year = $release_year;
            $album->type = $type;
            return response()->json([
                "status" => "success",
                "message" => 'Album uploaded successfully!
            All songs uploaded will be ready in some minutes and saved as private by default when finish.
            Please go to Account/Upload Management/Song to update song infomation.',
            ]);
        } finally {
            for ($i = 0; $i < $songCount; $i++) {
                $song = new Song();
                $song->song_id = "song_" . date("YmdHi") . Str::random(10);
                $song->user_id = Auth::user()->user_id;
                $song->title = $request["songName_" . $i];
                $song->display = "private";
                $song->listens = 0;
                if ($request->file("songFile_" . $i)) {
                    try {
                        $songFile = $request->file("songFile_" . $i);
                        $filename =
                            date("YmdHi") . $songFile->getClientOriginalName();
                        $songFile->move(
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
                            public_path("storage/upload/song_src/") .
                                "/" .
                                $filename
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
                $song->album_id = $album_id;
                $song->save();
            }
            $album->save();
        }
    }

    public function getUploadedAlbum()
    {
        $albumUploaded = DB::table("albums")
            ->leftJoin("songs", "albums.album_id", "=", "songs.album_id")
            ->select("albums.*", DB::raw("count(songs.song_id) as songCount"))
            ->where("albums.user_id", Auth::user()->user_id)
            ->groupBy("albums.id")
            ->get();
        return response()->json([
            "status" => "success",
            "album" => $albumUploaded,
        ]);
    }

    public function getAlbumInfo($id)
    {
        $album = Album::where("album_id", $id)->first();
        $songs = Song::where("album_id", $id)->count();
        $album->song_count = $songs;
        return response()->json([
            "status" => "success",
            "album" => $album,
        ]);
    }

    public function getAlbumSongs($id)
    {
        $songList = Song::where("album_id", $id)
            ->where("display", "public")
            ->with([
                "artist",
                "album",
                "like" => function ($query) {
                    $query->where("user_id", Auth::user()->user_id);
                },
            ])
            ->get();
        return response()->json([
            "status" => "success",
            "songs" => $songList,
        ]);
    }

    public function removeAlbumSong(Request $request)
    {
        $song = Song::where("song_id", $request->song_id)->first();
        if ($song->user_id != Auth::user()->user_id) {
            return response()->json([
                "status" => "error",
                "message" => "You are not allowed to delete this song!",
            ]);
        }
        $song->album_id = null;
        $song->save();
        return response()->json([
            "status" => "success",
            "message" => "Song removed successfully!",
        ]);
    }

    public function addAlbumSong(Request $request)
    {
        $song = Song::where("song_id", $request->song_id)->first();
        if ($song->user_id != Auth::user()->user_id) {
            return response()->json([
                "status" => "error",
                "message" => "You are not allowed to delete this song!",
            ]);
        }
        $album = Album::where("album_id", $request->album_id)->first();
        if (!$album) {
            return response()->json([
                "status" => "error",
                "message" => "Album not found!",
            ]);
        }
        $song->album_id = $request->album_id;
        $song->save();
        return response()->json([
            "status" => "success",
            "message" => "Song removed successfully!",
        ]);
    }

    public function updateAlbum(Request $request)
    {
        $albumData = json_decode($request->albumData);
        $album = Album::where("album_id", $albumData->album_id)->first();
        if (!$album) {
            return response()->json([
                "status" => "error",
                "message" => "Album not found!",
            ]);
        }
        if ($album->user_id != Auth::user()->user_id) {
            return response()->json([
                "status" => "error",
                "message" => "You are not allowed to update this album!",
            ]);
        }
        if ($request->file("image")) {
            $imageFile = $request->file("image");
            $fileName =
                $album->album_id .
                "." .
                $imageFile->getClientOriginalExtension();
            @unlink(
                public_path("storage/upload/album_cover") .
                    "/" .
                    $album->image_path
            );
            $imageFile->move(
                public_path("storage/upload/album_cover"),
                $fileName
            );
            $album->image_path = $fileName;
        }
        $album->name = $albumData->name;
        $album->release_year = $albumData->release_year;
        $album->type = $albumData->type;
        $album->save();
        return response()->json(
            [
                "status" => "success",
                "message" => "Album updated successfully!",
            ],
            200
        );
    }

    public function deleteAlbum($id)
    {
        $album = Album::find($id);
        if (!$album->id) {
            return response()->json([
                "status" => "error",
                "message" => "Album not found",
            ]);
        }
        if ($album->user_id != Auth::user()->user_id) {
            return response()->json([
                "status" => "error",
                "message" => "You are not allowed to delete this album!",
            ]);
        }
        @unlink(
            public_path("storage/upload/album_cover/") . "/" . $album->album_id
        );
        $song = Song::where("album_id", $album->album_id)->get();
        foreach ($song as $s) {
            $s->album_id = null;
            $s->save();
        }
        $album->delete();
        return response()->json([
            "status" => "success",
            "message" => "Album deleted successfully!",
        ]);
    }

    public function getLatestAlbum()
    {
        $album = DB::table("albums")
            ->join("users", "users.user_id", "=", "albums.user_id")
            ->leftJoin("songs", "songs.album_id", "=", "albums.album_id")
            ->select(
                "albums.*",
                "users.name as user_name",
                DB::raw("count(songs.song_id) as songCount")
            )
            ->orderBy("albums.created_at", "desc")
            ->groupBy("albums.album_id")
            ->take(8)
            ->get();
        return response()->json([
            "status" => "success",
            "albums" => $album,
        ]);
    }

    public function searchAlbum(Request $request)
    {
        if ($request->query->has("query")) {
            $query = $request->query->get("query");
            $albums = DB::table("albums")
                ->leftJoin("songs", "songs.album_id", "=", "albums.album_id")
                ->select(
                    "albums.*",
                    DB::raw("count(songs.song_id) as song_count")
                )
                ->groupBy("albums.album_id")
                ->where("name", "like", "%" . $query . "%")
                ->get();
            return response()->json([
                "status" => "success",
                "albums" => $albums,
            ]);
        } else {
            return response()->json([
                "status" => "error",
                "message" => "Query not found",
            ]);
        }
    }

    public function getTopAlbum()
    {
        $album = DB::table("albums")
            ->join("users", "users.user_id", "=", "albums.user_id")
            ->leftJoin("songs", "songs.album_id", "=", "albums.album_id")
            ->select(
                "albums.*",
                "users.name as user_name",
                DB::raw("sum(songs.listens) as totalListen")
            )
            ->orderBy("totalListen", "desc")
            ->groupBy("albums.album_id")
            ->take(8)
            ->get();
        return response()->json([
            "status" => "success",
            "albums" => $album,
        ]);
    }

    public function getSongNotInAlbum(Request $request)
    {
        $query = urldecode($request->get("query"));
        $songs = Song::with("artist")
            ->where("user_id", Auth::user()->user_id)
            ->where("title", "LIKE", "%" . $query . "%")
            ->where("album_id", null)
            ->orWhere("album_id", "")
            ->get();
        return response()->json([
            "status" => "success",
            "songs" => $songs,
        ]);
    }
}
