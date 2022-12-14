import { environment } from "@/environment/environment";

export const albumModule = {
  namespaced: true,
  state: {},
  getters: {},
  mutations: {},
  actions: {
    getAlbum(context: any, payload: { token: string; album_id: string }) {
      return fetch(`${environment.api}/album/${payload.album_id}`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${payload.token}`,
          Accept: "application/json",
        },
      });
    },
    getUploadedAlbums(context: any, userToken: string): Promise<Response> {
      // getUploadedAlbums
      return fetch(`${environment.api}/account/album/uploaded`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${userToken}`,
        },
      });
    },
    searchAlbums(
      context: any,
      data: { token: string; query: string; signal: AbortSignal }
    ) {
      return fetch(`${environment.api}/album/search?query=${data.query}`, {
        method: "GET",
        signal: data.signal,
        headers: {
          Accept: "application/json",
          Authorization: `Bearer ${data.token}`,
        },
      });
    },
    getAlbumSongs(
      context: any,
      payload: { userToken: string; album_id: string }
    ): Promise<Response> {
      return fetch(`${environment.api}/album/${payload.album_id}/song`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${payload.userToken}`,
          Accept: "application/json",
        },
      });
    },
    removeAlbumSongs(
      context: any,
      payload: { userToken: string; songId: string }
    ): Promise<Response> {
      return fetch(`${environment.api}/account/album/song-remove`, {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${payload.userToken}`,
          Accept: "application/json",
        },
        body: JSON.stringify({ song_id: payload.songId }),
      });
    },
    addAlbumSongs(
      context: any,
      payload: { userToken: string; songId: string; albumId: string }
    ): Promise<Response> {
      return fetch(`${environment.api}/account/album/song-add`, {
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
    updateAlbumInfo(
      context: any,
      payload: { userToken: string; albumInfo: FormData }
    ): Promise<Response> {
      return fetch(`${environment.api}/account/album/update`, {
        method: "POST",
        headers: {
          Authorization: `Bearer ${payload.userToken}`,
          Accept: "application/json",
        },
        body: payload.albumInfo,
      });
    },
    getAddableSong(
      context: any,
      payload: { userToken: string; signal: AbortSignal; query: string }
    ): Promise<Response> {
      return fetch(
        `${environment.api}/account/album/addable?query=${payload.query}`,
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
    deleteAlbum(context: any, payload: { userToken: string; albumId: string }) {
      return fetch(
        `${environment.api}/account/album/${payload.albumId}/delete`,
        {
          method: "DELETE",
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${payload.userToken}`,
          },
        }
      );
    },
    getLatestAlbums(context: any, userToken: string): Promise<Response> {
      return fetch(`${environment.api}/album/latest`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${userToken}`,
        },
      });
    },
    getTopAlbums(context: any, userToken: string): Promise<Response> {
      return fetch(`${environment.api}/album/top`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${userToken}`,
        },
      });
    },
    getAlbumById(
      context: any,
      payload: { userToken: string; albumId: string }
    ) {
      return fetch(`${environment.api}/album/${payload.albumId}`, {
        method: "GET",
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${payload.userToken}`,
        },
      });
    },
  },
};
