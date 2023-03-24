<style>
    #gallery {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        background-color: rgba(5,5,5,0.7);
        padding: 10px;
        height: 100%;
    }

    #gallery div {
        position: relative;
        padding-bottom: 56.25%;
    }

    #gallery div iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    .youtubeshow {
        position: absolute;
        top: 0;
        left: 5%;
        height: 80%;
        width: 60%;
        ;
        z-index: 2;
    }

    .content {
        z-index: 5;
    }

    @media (max-width: 945px) {
        #gallery {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        .youtubeshow {
            top: 0px;
            left: 0px;
            width: 100%;
            z-index: 2;
        }

        .youtubeshow iframe {
            height: 45%;
            width: 155%;
            z-index: 2;
        }
    }
</style>

<section id="two" class="spotlight style2 right">
    <span class="image fit main">
        <img src="./assets/css/images/motor v-strom.jpg" alt="" />
    </span>
    <div id="gallery" class="youtubeshow">
        <?php
        require_once './vendor/autoload.php';
        $apiKey = 'AIzaSyD8gc7DdatHVN1zNAgbJkoMl3Be-z_sm3s';
        $channelId = 'UC_rUL6tWuwx-iACNG_uHZVA';
        $client = new Google_Client();
        $client->setDeveloperKey($apiKey);
        $youtube = new Google_Service_YouTube($client);
        $params = array(
            'channelId' => $channelId,
            'type' => 'video',
            'order' => 'viewCount',
            'maxResults' => 10,
        );
        $cacheFile = 'youtube_videos_cache.json';
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < 60 * 60 * 24)) {
            $videos = json_decode(file_get_contents($cacheFile), true);
        } else {
            try {
                $searchResponse = $youtube->search->listSearch('id,snippet', $params);
                $videos = array();
                foreach ($searchResponse['items'] as $item) {
                    if ($item['id']['kind'] == 'youtube#video') {
                        $videoId = $item['id']['videoId'];
                        $title = $item['snippet']['title'];
                        $description = $item['snippet']['description'];
                        $thumbnail = $item['snippet']['thumbnails']['medium']['url'];
                        $videos[] = array(
                            'videoId' => $videoId,
                            'title' => $title,
                            'description' => $description,
                            'thumbnail' => $thumbnail,
                        );
                    }
                }
                file_put_contents($cacheFile, json_encode($videos));
            } catch (Exception $e) {
                $videos = json_decode(file_get_contents($cacheFile), true);
            }
        }
        $totalVideos = count($videos);

        $numVideosToShow = 9;
        shuffle($videos);
        for ($i = 0; $i < $numVideosToShow; $i++) {
            if ($i < count($videos)) {
                $video = $videos[$i];
                echo '<div class="video">';
                echo '<iframe src="https://www.youtube.com/embed/' . $video['videoId'] . '" allowfullscreen></iframe>';
                echo '<h3>' . $video['title'] . '</h3>';
                echo '</div>';
            }
        }
        ?>
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