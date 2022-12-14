import { environment } from "@/environment/environment";
import type { genre } from "@/model/genreModel";

export const adminModule = {
  namespaced: true,
  state: {},
  getters: {},
  mutations: {},
  actions: {
    getAllUsers(context: any, token: string) {
      return fetch(`${environment.api}/admin/users`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });
    },
    getUser(
      context: any,
      { token, user_id }: { token: string; user_id: string }
    ) {
      return fetch(`${environment.api}/admin/users/${user_id}`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });
    },
    getUserUploadSong(
      context: any,
      { token, user_id }: { token: string; user_id: string }
    ) {
      return fetch(`${environment.api}/admin/users/${user_id}/songs`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });
    },
    getUserUploadAlbum(
      context: any,
      { token, user_id }: { token: string; user_id: string }
    ) {
      return fetch(`${environment.api}/admin/users/${user_id}/albums`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });
    },
    deleteUser(
      context: any,
      { token, user_id }: { token: string; user_id: string }
    ) {
      return fetch(`${environment.api}/admin/users/${user_id}/delete`, {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });
    },
    changeUserRole(
      context: any,
      { token, user_id }: { token: string; user_id: string }
    ) {
      return fetch(`${environment.api}/admin/users/${user_id}/role/update`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });
    },
    getAllSongs(
      context: any,
      data: {
        token: string;
        page: number;
        filterText: string;
        filterType: string;
        signal: AbortSignal;
        itemPerPage: number;
      }
    ) {
      return fetch(
        `${environment.api}/admin/songs?page=${data.page}&itemPerPage=${data.itemPerPage}&query=${data.filterText}&type=${data.filterType}`,
        {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${data.token}`,
            Accept: "application/json",
          },
          signal: data.signal,
        }
      );
    },
    getSong(
      context: any,
      { token, song_id }: { token: string; song_id: string }
    ) {
      return fetch(`${environment.api}/admin/songs/${song_id}`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });
    },
    updateSong(
      context: any,
      data: { token: string; songForm: FormData }
    ): Promise<Response> {
      return fetch(`${environment.api}/admin/songs/update`, {
        method: "POST",
        headers: {
          Accept: "multipart/form-data",
          Authorization: `Bearer ${data.token}`,
        },
        body: data.songForm,
      });
    },
    deleteSong(
      context: any,
      { token, song_id }: { token: string; song_id: string }
    ) {
      return fetch(`${environment.api}/admin/songs/${song_id}/delete`, {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });
    },
    getAllAlbums(
      context: any,
      data: {
        token: string;
        page: number;
        filterText: string;
        filterType: string;
        signal: AbortSignal;
        itemPerPage: number;
      }
    ) {
      return fetch(
        `${environment.api}/admin/albums?page=${data.page}&itemPerPage=${data.itemPerPage}&query=${data.filterText}&type=${data.filterType}`,
        {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${data.token}`,
            Accept: "application/json",
          },
          signal: data.signal,
        }
      );
    },
    getAlbum(
      context: any,
      { token, album_id }: { token: string; album_id: string }
    ) {
      return fetch(`${environment.api}/admin/albums/${album_id}`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`,
          Accept: "application/json",
        },
      });
    },
    updateAlbumInfo(
      context: any,
      payload: { userToken: string; albumInfo: FormData }
    ): Promise<Response> {
      return fetch(`${environment.api}/admin/albums/update`, {
        method: "POST",
        headers: {
          Authorization: `Bearer ${payload.userToken}`,
          Accept: "application/json",
        },
        body: payload.albumInfo,
      });
    },
    removeAlbumSongs(
      context: any,
      payload: { userToken: string; songId: string }
    ): Promise<Response> {
      return fetch(`${environment.api}/admin/albums/song-remove`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${payload.userToken}`,
          Accept: "application/json",
        },
        body: JSON.stringify({ song_id: payload.songId }),
      });
    },
    deleteAlbum(
      context: any,
      payload: { userToken: string; albumId: string }
    ): Promise<Response> {
      return fetch(
        `${environment.api}/admin/albums/${payload.albumId}/delete`,
        {
          method: "DELETE",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${payload.userToken}`,
            Accept: "application/json",
          },
        }
      );
    },
    getAddableSong(
      context: any,
      payload: { userToken: string; signal: AbortSignal; query: string }
    ): Promise<Response> {
      return fetch(
        `${environment.api}/admin/albums/addable?query=${payload.query}`,
        {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${payload.userToken}`,
            Accept: "application/json",
          },
          signal: payload.signal,
        }
      );
    },
    getAlbumSongs(
      context: any,
      payload: { userToken: string; album_id: string }
    ): Promise<Response> {
      return fetch(`${environment.api}/admin/albums/${payload.album_id}/song`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${payload.userToken}`,
          Accept: "application/json",
        },
      });
    },
    addAlbumSongs(
      context: any,
      payload: { userToken: string; songId: string; albumId: string }
    ): Promise<Response> {
      return fetch(`${environment.api}/admin/albums/song-add`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${payload.userToken}`,
          Accept: "application/json",
        },
        body: JSON.stringify({
          song_id: payload.songId,
          album_id: payload.albumId,
        }),
      });
    },
    getArtists(
      context: any,
      payload: {
        userToken: string;
        signal: AbortSignal;
        query: string;
        itemPerPage: number;
        currentPage: number;
      }
    ) {
      return fetch(
        `${environment.api}/admin/artists?query=${payload.query}&itemPerPage=${payload.itemPerPage}&page=${payload.currentPage}`,
        {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${payload.userToken}`,
            Accept: "application/json",
          },
          signal: payload.signal,
        }
      );
    },
    getArtist(context: any, payload: { userToken: string; artistId: string }) {
      return fetch(`${environment.api}/admin/artists/${payload.artistId}`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${payload.userToken}`,
        },
      });
    },
    getArtistSongs(
      context: any,
      payload: {
        token: string;
        page: number;
        filterText: string;
        filterType: string;
        signal: AbortSignal;
        itemPerPage: number;
        currentPage: number;
        artistId: string;
      }
    ) {
      return fetch(
        `${environment.api}/admin/artists/${payload.artistId}/song?query=${payload.filterText}&itemPerPage=${payload.itemPerPage}&page=${payload.currentPage}`,
        {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${payload.token}`,
          },
          signal: payload.signal,
        }
      );
    },
    updateArtist(
      context: any,
      payload: { userToken: string; artistData: FormData }
    ) {
      return fetch(`${environment.api}/admin/artists/update`, {
        method: "POST",
        headers: {
          Authorization: `Bearer ${payload.userToken}`,
          Accept: "application/json",
        },
        body: payload.artistData,
      });
    },
    groupArtist(
      context: any,
      payload: { userToken: string; from: string; to: string }
    ) {
      return fetch(`${environment.api}/admin/artists/group`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${payload.userToken}`,
          Accept: "application/json",
        },
        body: JSON.stringify({
          from: payload.from,
          to: payload.to,
        }),
      });
    },
    deleteArtist(
      context: any,
      payload: { userToken: string; artistId: string }
    ) {
      return fetch(
        `${environment.api}/admin/artists/${payload.artistId}/delete`,
        {
          method: "DELETE",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${payload.userToken}`,
            Accept: "application/json",
          },
        }
      );
    },
    getGenres(
      context: any,
      payload: {
        userToken: string;
        page: number;
        query: string;
        filterType: string;
        signal: AbortSignal;
        itemPerPage: number;
        currentPage: number;
      }
    ) {
      return fetch(
        `${environment.api}/admin/genres?query=${payload.query}&itemPerPage=${payload.itemPerPage}&page=${payload.currentPage}`,
        {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${payload.userToken}`,
          },
          signal: payload.signal,
        }
      );
    },
    getGenre(context: any, payload: { userToken: string; genre_id: string }) {
      return fetch(`${environment.api}/admin/genres/${payload.genre_id}`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${payload.userToken}`,
          Accept: "application/json",
        },
      });
    },
    updateGenre(context: any, payload: { userToken: string; genre: genre }) {
      return fetch(`${environment.api}/admin/genres/update`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${payload.userToken}`,
          Accept: "application/json",
        },
        body: JSON.stringify(payload.genre),
      });
    },
    deleteGenre(
      context: any,
      payload: { userToken: string; genre_id: string }
    ) {
      return fetch(
        `${environment.api}/admin/genres/${payload.genre_id}/delete`,
        {
          method: "DELETE",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${payload.userToken}`,
            Accept: "application/json",
          },
        }
      );
    },
    getGenreSong(
      context: any,
      payload: {
        userToken: string;
        genre_id: string;
        page: number;
        query: string;
        signal: AbortSignal;
        itemPerPage: number;
        currentPage: number;
      }
    ) {
      return fetch(
        `${environment.api}/admin/genres/${payload.genre_id}/songs?query=${payload.query}&itemPerPage=${payload.itemPerPage}&page=${payload.currentPage}`,
        {
          method: "GET",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${payload.userToken}`,
            Accept: "application/json",
          },
          signal: payload.signal,
        }
      );
    },
    groupGenre(
      context: any,
      payload: { userToken: string; from: string; to: string }
    ) {
      return fetch(`${environment.api}/admin/genres/group`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${payload.userToken}`,
          Accept: "application/json",
        },
        body: JSON.stringify({
          from: payload.from,
          to: payload.to,
        }),
      });
    },
  },
};
