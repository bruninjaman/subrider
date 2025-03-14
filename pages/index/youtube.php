<style>
    #gallery {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        background-color: rgba(5, 5, 5, 0.7);
        height: 100%;
        width: 58%;
        max-width: 58%;
        padding: auto;
    }

    #gallery article {
        background-color: rgba(5, 5, 5, 0.7);
        border-radius: 5px;
        max-height: 80%;
        margin: 2%;
        box-shadow: inset 0 0 10px #000000;
    }

    #gallery article:hover {
        background-color: rgba(255, 5, 5, 0.4);
    }

    #gallery a {
        border-bottom: 0;
    }


    #gallery h2:hover {
        color: rgba(255, 255, 255, 1);
    }

    #gallery h2 {
        font-size: 18px;
        padding: 5%;
        color: rgba(255, 255, 255, 0.5);
        text-align: center;
    }

    #gallery div {
        position: relative;
    }

    .fancybox img {
        width: 100%;
        height: 50%;
        padding: 2%;
        border-radius: 10px;
    }

    .youtubeshow {
        position: absolute;
        top: 0;
        left: 2%;
        height: 80%;
        width: 60%;
        z-index: 2;
    }

    @media screen and (max-width: 736px) {
        .youtubevideos {
            position: absolute;
            top: 40%;
            font-size: 5em;
            font-family: "Calibri", Helvetica, sans-serif;
            font-weight: 800;
            left: 6rem;
        }
    }

    /* Responsive styles */
    @media only screen and (max-width: 980px) {
        #gallery {
            grid-template-columns: repeat(1, 1fr);
            width: 100%;
            /* Use full width on smaller screens */
            padding: 10%;
            max-width: 100%;
        }

        #gallery article {
            max-height: 100%;
            border-radius: 25px;
        }

        .fancybox img {
            border-radius: 20px;
        }

        #gallery div {
            padding-top: 0;
        }

        .youtubeshow {
            position: static;
            /* Change position to static to allow for stacking of elements */
            height: auto;
            /* Remove fixed height to allow the iframe to scale down */
            width: 100%;
            /* Use full width on smaller screens */
            margin-bottom: 20px;
            /* Add some space between the elements */
            z-index: 0;
            /* Set z-index to 0 to allow the iframe to be stacked behind the other elements */
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
            $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
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
                // Shuffle for randomness
                shuffle($videos);

                // Limit to desired number
                $videos = array_slice($videos, 0, $numVideosToShow);

                foreach ($videos as $video) {
                    $videoId = sanitizeOutput($video['videoId']);
                    $title = sanitizeOutput($video['title']);

                    echo '<article class="video">';
                    echo '<figure>';
                    echo '<a class="fancybox fancybox.iframe" href="https://www.youtube.com/embed/' . $videoId . '">';
                    echo '<img class="videoThumb" src="https://i1.ytimg.com/vi/' . $videoId . '/mqdefault.jpg" alt="' . $title . '">';
                    echo '</a>';
                    echo '</figure>';
                    echo '<h2 class="videoTitle">' . $title . '</h2>';
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