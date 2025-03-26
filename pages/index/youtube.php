<style>
    #gallery {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        background-color: rgba(15, 15, 15, 0.95);
        width: 58%;
        max-width: 58%;
        padding: 24px;
        border-radius: 12px;
    }

    #gallery article {
        background-color: transparent;
        border-radius: 12px;
        transition: transform 0.2s ease-in-out;
        cursor: pointer;
    }

    #gallery article:hover {
        transform: scale(1.05);
        background-color: rgba(32, 32, 32, 0.95);
    }

    #gallery a {
        border-bottom: 0;
        display: block;
    }

    #gallery h2 {
        font-size: 14px;
        padding: 12px;
        color: rgba(255, 255, 255, 0.9);
        text-align: left;
        font-weight: 500;
        margin: 0;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    #gallery div {
        position: relative;
    }

    .fancybox img {
        width: 100%;
        height: auto;
        border-radius: 12px;
        aspect-ratio: 16/9;
        object-fit: cover;
    }

    .youtubeshow {
        position: absolute;
        top: 0;
        left: 2%;
        height: auto;
        width: 60%;
        z-index: 2;
    }

    .video-duration {
        position: absolute;
        bottom: 8px;
        right: 8px;
        background-color: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 2px 4px;
        border-radius: 4px;
        font-size: 12px;
    }

    .video-info {
        display: flex;
        padding: 12px;
        gap: 12px;
    }

    .channel-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        overflow: hidden;
    }

    .channel-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .video-details {
        flex: 1;
    }

    .video-metadata {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.6);
        margin-top: 4px;
    }

    .channel-name {
        color: rgba(255, 255, 255, 0.9);
        font-weight: 500;
    }

    .video-description {
        margin-top: 8px;
        font-size: 12px;
        line-height: 1.4;
        color: rgba(255, 255, 255, 0.6);
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    @media screen and (max-width: 980px) {
        #gallery {
            grid-template-columns: repeat(2, 1fr);
            width: 100%;
            max-width: 100%;
            padding: 16px;
        }
    }

    @media screen and (max-width: 736px) {
        #gallery {
            grid-template-columns: 1fr;
        }

        .youtubevideos {
            font-size: 3em;
            text-align: center;
            margin-bottom: 20px;
        }

        .youtubeshow {
            position: static;
            width: 100%;
            margin: 0;
            padding: 16px;
        }
    }
</style>

<section id="two" class="spotlight style2 right">
    <span class="image fit main">
        <img src="./assets/css/images/motor v-strom.jpg" alt="" />
        <h1 class="youtubevideos">Videos</h1>
    </span>
    <div>
        <div id="gallery" class="youtubeshow">
            <?php
            require_once './vendor/autoload.php';

            // Load environment variables from .env file
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
            $dotenv->load();

            // Store API key in environment variable
            $apiKey = $_ENV['YOUTUBE_API_KEY'];
            $channelId = $_ENV['YOUTUBE_CHANNEL_ID'];

            // Cache settings
            $cacheFile = 'youtube_videos_cache.json';
            $cacheDuration = 60 * 60 * 24; // 24 hours
            $videos = [];
            $numVideosToShow = 9;

            // Function to sanitize output
            function sanitizeOutput($string)
            {
                return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
            }

            // Check cache first
            if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < $cacheDuration)) {
                $cachedData = file_get_contents($cacheFile);
                if ($cachedData !== false) {
                    $videos = json_decode($cachedData, true) ?: [];
                }
            }

            // If cache is empty or expired, fetch from API
            if (empty($videos)) {
                try {
                    $client = new Google_Client();
                    $client->setDeveloperKey($apiKey);
                    $youtube = new Google_Service_YouTube($client);

                    $params = [
                        'channelId' => $channelId,
                        'type' => 'video',
                        'order' => 'viewCount',
                        'maxResults' => 20, // Fetch more than needed for variety
                    ];

                    $searchResponse = $youtube->search->listSearch('id,snippet', $params);

                    foreach ($searchResponse['items'] as $item) {
                        if (isset($item['id']['kind']) && $item['id']['kind'] == 'youtube#video') {
                            $videoId = $item['id']['videoId'] ?? '';
                            $title = $item['snippet']['title'] ?? '';
                            $description = $item['snippet']['description'] ?? '';
                            $thumbnail = $item['snippet']['thumbnails']['medium']['url'] ?? '';

                            if (!empty($videoId)) {
                                $videos[] = [
                                    'videoId' => $videoId,
                                    'title' => $title,
                                    'description' => $description,
                                    'thumbnail' => $thumbnail,
                                ];
                            }
                        }
                    }

                    // Save to cache if we got results
                    if (!empty($videos)) {
                        $cacheDir = dirname($cacheFile);
                        if (!is_dir($cacheDir)) {
                            mkdir($cacheDir, 0755, true);
                        }
                        file_put_contents($cacheFile, json_encode($videos), LOCK_EX);
                    }
                } catch (Exception $e) {
                    // Log error instead of exposing it
                    error_log('YouTube API error: ' . $e->getMessage());

                    // Try to load from cache as fallback, even if expired
                    if (file_exists($cacheFile)) {
                        $videos = json_decode(file_get_contents($cacheFile), true) ?: [];
                    }
                }
            }

            // Display videos
            if (!empty($videos)) {
                shuffle($videos);
                $videos = array_slice($videos, 0, $numVideosToShow);

                foreach ($videos as $video) {
                    $videoId = sanitizeOutput($video['videoId']);
                    $title = sanitizeOutput($video['title']);

                    echo '<article class="video">';
                    echo '<a class="fancybox fancybox.iframe" href="https://www.youtube.com/embed/' . $videoId . '">';
                    echo '<div class="thumbnail-container">';
                    echo '<img class="videoThumb" src="https://i1.ytimg.com/vi/' . $videoId . '/mqdefault.jpg" alt="' . $title . '">';
                    echo '<span class="video-duration">4:20</span>';
                    echo '</div>';
                    echo '</a>';
                    echo '<div class="video-info">';
                    echo '<div class="channel-avatar">';
                    echo '<img src="https://yt3.googleusercontent.com/ytc/AIdro_loDpUcFuLazvXAyiSh2OcszZvHkz1s0TUG0G0lGWKDaw=s160-c-k-c0x00ffffff-no-rj" alt="Canal Avatar">';
                    echo '</div>';
                    echo '<div class="video-details">';
                    echo '<h2 class="videoTitle">' . $title . '</h2>';
                    echo '<div class="video-metadata">';
                    echo '<span class="channel-name">Descrição</span><br>';
                    echo '<div class="video-description">' . (strlen($video['description']) > 100 ? substr(sanitizeOutput($video['description']), 0, 100) . '...' : sanitizeOutput($video['description'])) . '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                    echo '</article>';
                }
            } else {
                echo '<p>No videos available at this time.</p>';
            }
            ?>
        </div>
    </div>


    <div class="content">
        <header>
            <h2>Canal de videos no youtube</h2>
            <a href="https://www.youtube.com/channel/UC_rUL6tWuwx-iACNG_uHZVA?sub_confirmation=1" target="_blank">Acesse o canal</a>
        </header>
        <p>Mostramos os nossos serviços de manutenção e estética em motocicletas com nosso trabalho, especialização, ferramentas e tecnologias.</p>
        <ul class="actions">
            <li><a href="https://www.youtube.com/channel/UC_rUL6tWuwx-iACNG_uHZVA?sub_confirmation=1" target="_blank" class="button">Inscreva-se</a></li>
        </ul>
    </div>
    <a href="#three" class="goto-next scrolly">Proximo</a>
</section>