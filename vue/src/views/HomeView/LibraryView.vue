<template>
  <div class="container">
    <BaseCardAlbum
      v-for="playlist in userPlaylist"
      :key="playlist.playlist_id"
      :title="playlist.title"
      :id="playlist.playlist_id"
      :img="
        playlist.image_path
          ? `${environment.playlist_cover}/${playlist.image_path}`
          : `${environment.default}/no_image.jpg`
      "
      :type="'playlist'"
      :songCount="playlist.songCount"
      @playPlaylist="playPlaylist"
    />
  </div>
</template>
<script lang="ts">
import type { playlist } from "@/model/playlistModel";
import { defineComponent } from "vue";
import { mapGetters } from "vuex";
import BaseCardAlbum from "../../components/UI/BaseCardAlbum.vue";
import { environment } from "@/environment/environment";

type playlistData = playlist & {
  songCount: number;
};

export default defineComponent({
  components: { BaseCardAlbum },
  computed: {
    ...mapGetters({
      playlist: "playlist/getPlaylist",
    }),
  },
  inject: {
    userPlaylist: {
      from: "userPlaylist",
      default: [] as playlistData[],
    },
  },
  methods: {
    playPlaylist(id: string) {
      this.$emit("playPlaylist", id);
    },
  },
  emits: [
    "updatePlaylist",
    "deletePlaylist",
    "playPlaylist",
    "playSongInPlaylist",
    "addPlaylistToQueue",
    "playAlbum",
    "addAlbumToQueue",
    "playSongInAlbum",
    "playArtistSong",
    "playSongOfArtist",
    "addArtistSongToQueue",
    "playLikedSong",
    "addLikedSongToQueue",
    "addToQueue",
    "playSong",
  ],
  data() {
    return {
      environment: environment,
    };
  },
});
</script>
<style lang="scss" scoped>
.container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: 100%;
  flex-wrap: wrap;
}
</style>
