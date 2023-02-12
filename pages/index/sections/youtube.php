<style>
    .youtubeshow {
        position: absolute;
        top: 100px;
        left: 60px;
        z-index: 2;
    }

    @media (max-width: 767px) {
        .youtubeshow {
            top: 20px;
            left: 20px;
            z-index: 2;
        }
        .youtubeshow iframe{
            height: 190px;
            width: 360px;
            z-index: 2;
        }
    }
</style>
<section id="two" class="spotlight style2 right">
    <span class="image fit main">
        <img src="./assets/css/images/motor v-strom.jpg" alt="" />
    </span>
    <div class="youtubeshow">
        <?php
        // Load the Google client library
        require_once './vendor/autoload.php';

        // Define the YouTube API key
        $apiKey = 'AIzaSyD8gc7DdatHVN1zNAgbJkoMl3Be-z_sm3s';

        // Define the YouTube channel ID
        $channelId = 'UC_rUL6tWuwx-iACNG_uHZVA';

        // Create a new Google client object
        $client = new Google_Client();
        $client->setDeveloperKey($apiKey);

        // Create a new YouTube service object
        $youtube = new Google_Service_YouTube($client);

        // Define the parameters for the search
        $params = array(
            'channelId' => $channelId,
            'type' => 'video',
            'order' => 'viewCount',
            'maxResults' => 10,
        );

        // Perform the search
        $searchResponse = $youtube->search->listSearch('id,snippet', $params);

        // Get the total number of search results
        $totalResults = count($searchResponse['items']);

        // Generate a random index between 0 and the total number of search results
        $randomIndex = rand(0, $totalResults - 1);

        // Get the random video from the search results
        $randomVideo = $searchResponse['items'][$randomIndex];

        // Get the video ID
        $videoId = $randomVideo['id']['videoId'];

        // Display the video
        echo '<iframe width="840" height="472" src="https://www.youtube.com/embed/' . $videoId . '" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
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