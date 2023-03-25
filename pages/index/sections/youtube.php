<style>
    #gallery {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 10px;
        background-color: rgba(5, 5, 5, 0.7);
        height: 100%;
        width: 58%;
        max-width: 58%;
        padding: 2%;
    }

    #gallery a {
        border-bottom: 0;
    }

    #gallery h2 {
        font-size: 18px;
        background-color: rgba(5, 5, 5, 0.7);
        border-radius: 5px;
        max-height: 40%;
        padding: 5%;
        text-align: center;
    }

    #gallery div {
        position: relative;
    }

    .fancybox img {
        width: 100%;
        height: 50%;
    }

    .youtubeshow {
        position: absolute;
        top: 0;
        left: 2%;
        height: 80%;
        width: 60%;
        z-index: 2;
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
    </span>
    <div>
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
                    echo '<article class="video">';
                    echo '<figure>';
                    echo '<a class="fancybox fancybox.iframe" href="//www.youtube.com/embed/' . $video['videoId'] . '"><img class="videoThumb" src="//i1.ytimg.com/vi/' . $video['videoId'] . '/mqdefault.jpg"></a>';
                    echo '</figure>';
                    echo '<h2 class="videoTitle">' . $video['title'] . '</h2>';
                    echo '</article>';
                }
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