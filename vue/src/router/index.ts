import store from "@/store";
import { createRouter, createWebHistory } from "vue-router";

import AccountView from "../views/AccountView/AccountView.vue";
import HomeView from "../views/HomeView/HomeView.vue";
import AuthView from "../views/AuthView/AuthView.vue";
import LogInView from "../views/AuthView/LogInView.vue";
import SignUpView from "../views/AuthView/SignUpView.vue";
import NotFoundView from "../views/NotFoundView.vue";

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: "/",
      name: "home",
      component: HomeView,
      redirect: { name: "mainPage" },
      meta: {
        requiresAuth: true,
      },
      children: [
        {
          path: "/home",
          name: "mainPage",
          component: () => import("../views/HomeView/MainPageView.vue"),
        },
        {
          path: "/search",
          name: "searchPage",
          component: () => import("../views/HomeView/SearchPageView.vue"),
        },
        {
          path: "/library",
          name: "libraryPage",
          component: () => import("../views/HomeView/LibraryView.vue"),
        },
        {
          path: "/album/:id",
          name: "albumViewPage",
          component: () => import("../views/HomeView/AlbumView.vue"),
        },
        {
          path: "/playlist/:id",
          name: "playlistViewPage",
          component: () => import("../views/HomeView/PlaylistDetails.vue"),
        },
        {
          path: "/playlist/create",
          name: "playlistCreatePage",
          component: () => import("../views/HomeView/PlaylistCreateView.vue"),
        },
        {
          path: "/collection",
          name: "collectionPage",
          component: () => import("../views/HomeView/CollectionView.vue"),
        },
        {
          path: "upload",
          name: "uploadPage",
          component: () => import("../views/HomeView/UploadView.vue"),
        },
        {
          path: "playing",
          name: "playingPage",
          component: () => import("../views/HomeView/PlayingSongView.vue"),
        },
        {
          path: "user/:id",
          name: "profilePage",
          component: () => import("../views/HomeView/ProfileView.vue"),
        },
        {
          path: "artist/:id",
          name: "artistPage",
          redirect: (to) => ({
            name: "artistOverviewPage",
            params: { id: to.params.id },
          }),
          component: () =>
            import("../views/HomeView/ArtistDetailsView/ArtistDetailsView.vue"),
          children: [
            {
              path: "overview",
              name: "artistOverviewPage",
              component: () =>
                import(
                  "../views/HomeView/ArtistDetailsView/ArtistDetailsMain.vue"
                ),
            },
            {
              path: "albums",
              name: "artistAlbumsPage",
              component: () =>
                import(
                  "../views/HomeView/ArtistDetailsView/ArtistDetailsAlbum.vue"
                ),
            },
          ],
        },
      ],
    },

    {
      path: "/auth",
      redirect: { name: "login" },
      name: "auth",
      component: AuthView,
      meta: { isGuest: true },
      children: [
        {
          path: "/login",
          name: "login",
          component: LogInView,
        },
        {
          path: "/signup",
          name: "signup",
          component: SignUpView,
        },
        {
          path: "/forgot-password",
          name: "forgotPassword",
          component: () => import("../views/AuthView/ForgotPasswordView.vue"),
        },
        {
          path: "/recover-password",
          name: "recoverPassword",
          component: () => import("../views/AuthView/RecoverPasswordView.vue"),
        },
      ],
    },

    {
      path: "/account",
      name: "account",
      component: AccountView,
      redirect: { name: "accountOverview" },
      meta: {
        requiresAuth: true,
      },
      children: [
        {
          path: "overview",
          name: "accountOverview",
          component: () =>
            import("../views/AccountView/AccountOverviewView.vue"),
        },
        {
          path: "profile",
          name: "accountProfile",
          component: () =>
            import("../views/AccountView/AccountProfileView.vue"),
        },
        {
          path: "security",
          name: "accountSecurity",
          component: () =>
            import("../views/AccountView/AccountSecurityView.vue"),
        },
        {
          path: "avatar",
          name: "accountAvatar",
          component: () => import("../views/AccountView/AccountAvatarView.vue"),
        },
        {
          path: "upload",
          name: "accountUpload",
          redirect: { name: "accountUploadSong" },
          component: () =>
            import(
              "../views/AccountView/AccountUpload/AccountUploadManagementView.vue"
            ),
          children: [
            {
              path: "album",
              name: "accountUploadAlbum",
              component: () =>
                import(
                  "../views/AccountView/AccountUpload/AccountUploadAlbum/AccountUploadAlbum.vue"
                ),
            },
            {
              path: "album/edit",
              name: "accountUploadAlbumEdit",
              component: () =>
                import(
                  "../views/AccountView/AccountUpload/AccountUploadAlbum/AccountUploadAlbumEdit.vue"
                ),
            },
            {
              path: "song",
              name: "accountUploadSong",
              component: () =>
                import(
                  "../views/AccountView/AccountUpload/AccountUploadSong/AccountUploadSong.vue"
                ),
            },
            {
              path: "song/edit",
              name: "accountUploadSongEdit",
              component: () =>
                import(
                  "../views/AccountView/AccountUpload/AccountUploadSong/AccountUploadSongEdit.vue"
                ),
            },
          ],
        },
        {
          path: "admin",
          name: "accountAdmin",
          redirect: { name: "accountAdminDashboard" },
          component: () =>
            import("../views/AccountView/AccountAdmin/AccountAdminView.vue"),
          children: [
            {
              path: "dashboard",
              name: "accountAdminDashboard",
              component: () =>
                import(
                  "../views/AccountView/AccountAdmin/AccountAdminDashboard.vue"
                ),
            },
            {
              path: "user",
              name: "accountAdminUser",
              component: () =>
                import(
                  "../views/AccountView/AccountAdmin/AccountAdminUsers/AccountAdminUsers.vue"
                ),
            },
            {
              path: "user/:id",
              name: "accountAdminUserEdit",
              component: () =>
                import(
                  "../views/AccountView/AccountAdmin/AccountAdminUsers/AccountAdminUserEdit.vue"
                ),
            },
            {
              path: "disc",
              name: "accountAdminDisc",
              redirect: { name: "accountAdminDiscSong" },
              component: () =>
                import(
                  "../views/AccountView/AccountAdmin/AccountAdminDics/AccountAdminDiscsView.vue"
                ),
              children: [
                {
                  path: "song",
                  name: "accountAdminDiscSong",
                  component: () =>
                    import(
                      "../views/AccountView/AccountAdmin/AccountAdminDics/AccountAdminSongs.vue"
                    ),
                },
                {
                  path: "song/:id",
                  name: "accountAdminDiscSongEdit",
                  component: () =>
                    import(
                      "../views/AccountView/AccountAdmin/AccountAdminDics/AccountAdminSongEdit.vue"
                    ),
                },
                {
                  path: "album",
                  name: "accountAdminDiscAlbum",
                  component: () =>
                    import(
                      "../views/AccountView/AccountAdmin/AccountAdminDics/AccountAdminAlbums.vue"
                    ),
                },
                {
                  path: "album/:id",
                  name: "accountAdminDiscAlbumEdit",
                  component: () =>
                    import(
                      "../views/AccountView/AccountAdmin/AccountAdminDics/AccountAdminAlbumEdit.vue"
                    ),
                },
              ],
            },
            {
              path: "artists",
              name: "accountAdminArtists",
              component: () =>
                import(
                  "../views/AccountView/AccountAdmin/AccountAdminArtist/AccountAdminArtistView.vue"
                ),
            },
            {
              path: "artist/:id",
              name: "accountAdminArtistEdit",
              component: () =>
                import(
                  "../views/AccountView/AccountAdmin/AccountAdminArtist/AccountAdminArtistEdit.vue"
                ),
            },
            {
              path: "genres",
              name: "accountAdminGenres",
              component: () =>
                import(
                  "../views/AccountView/AccountAdmin/AccountAdminGenre/AccountAdminGenreView.vue"
                ),
            },
            {
              path: "genre/:id",
              name: "accountAdminGenreEdit",
              component: () =>
                import(
                  "../views/AccountView/AccountAdmin/AccountAdminGenre/AccountAdminGenreEdit.vue"
                ),
            },
          ],
        },
      ],
    },

    {
      path: "/verify-email",
      name: "verifyEmail",
      meta: {
        requiresAuth: true,
      },
      component: () => import("../views/VerifyEmailView.vue"),
    },

    {
      path: "/test",
      meta: {
        requiresAuth: true,
      },
      component: () => import("../views/test.vue"),
    },
    {
      path: "/:notFound(.*)",
      component: NotFoundView,
      name: "notFound",
    },
  ],
});

router.beforeEach((to, from, next) => {
  if (store.getters["auth/checkLocalToken"]) {
    if (!store.getters["auth/userToken"]) {
      store.commit("auth/checkToken");
      store
        .dispatch("auth/checkAuth")
        .then((res) => {
          if (res) {
            return res.json();
          }
        })
        .then((res) => {
          if (res.message === "Unauthenticated.") {
            store.commit("auth/clearUser");
            next({ name: "auth" });
          } else {
            const data = {
              user: {
                user_id: res.user_id,
                name: res.name,
                email: res.email,
                profile_photo_url: res.profile_photo_url,
                role: res.role,
              },
            };
            store.commit("auth/setUser", data);
            if (to.meta.requiresAuth) {
              next();
            } else {
              next({ name: "home" });
            }
          }
        });
    } else {
      if (to.meta.requiresAuth) {
        next();
      } else {
        //next();
        next({ name: "home" });
      }
    }
  } else {
    if (to.meta.isGuest) next();
    else next({ name: "auth" });
  }
});
export default router;
