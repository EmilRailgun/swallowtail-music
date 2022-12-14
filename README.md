## Swallowtail Music Player

Just a simple music player for the terminal. It's not very good, but it's mine. I build it for only learning purposes.

### Dependencies

-   [FFmpeg](https://ffmpeg.org/)
-   [vorbis](https://xiph.org/vorbis/)

### Installation

```bash
git clone https://github.com/EmilRailgun/swallowtail-music.git
```

-   For first setup

    ```bash
    cd swallowtail-music
    composer install
    cp .env.example .env
    php artisan key:generate
    php artisan migrate
    php artisan db:seed
    ```

-   To run server with default port 8000

    ```bash
    cd swallowtail-music
    php artisan serve
    ```

-   To run client

    ```bash
    cd swallowtail-music
    cd vue
    npm install
    npm run dev
    ```

## Feature

**Admin**

-   [x] Admin Theme
-   [x] Login, Logout
-   [x] User management and authentication
-   [x] CRUD Song/Album/Gernes/Artist
-   [x] Filter Database
-   [ ] Display Logs
-   [ ] Display Report
-   [x] Dashboard

**Client**

-   Authentication

    -   [x] Login, Logout
    -   [x] Register
    -   [x] Forgot Password
    -   [x] Reset Password
    -   [x] Verify Email

-   Song

    -   [x] Play Song
    -   [x] Add to Playlist
    -   [x] Add to Favorite
    -   [x] Search Song
    -   [x] Filter Song
    -   [x] Display Song
    -   [x] Upload Song
    -   [x] Edit Song

-   Album

    -   [x] Display Album
    -   [x] Filter Album
    -   [x] Search Album
    -   [x] Upload Album
    -   [x] Edit Album
    -   [x] Delete Album
    -   [x] Play Album

-   Playlist

    -   [x] Display Playlist
    -   [x] Filter Playlist
    -   [x] Search Playlist
    -   [x] Create Playlist
    -   [x] Edit Playlist
    -   [x] Delete Playlist
    -   [x] Play Playlist

-   Other

    -   [ ] Notification
    -   [x] Change Profile Picture
    -   [x] Edit Profile
