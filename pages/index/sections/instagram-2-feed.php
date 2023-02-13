<section id="subriderfeed" class="wrapper style2 special fade">
    <div class="container">
        <h2>Instagram Subrider</h2>
        <!-- <div id="instafeed" class="owl-carousel owl-theme owl-loaded owl-drag"></div> -->
        <?php
        // Define the access token
        $accessToken = 'IGQVJXaU1pRVBJUHZAwZAS03N0dCbzZAJT2ZA6OERDM2txMC1vdlNUbEtxeGR6ZAENqaHY5c3ZAab3RzMUNIWnNDMEdDbmttbEJYQkZAqeUp4VDR1eHROczZA4T1d1VW5VQWpVQ2VyLXRJSWxUdGNpajVJUG14dwZDZD';

        // Define the number of posts to retrieve
        $limit = 21;

        // Define the cache file name
        $cacheFile = 'instagram_feed_data.txt';

        // Check if the cached data is still valid
        if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < 60 * 60 * 24)) {
            // The cache is still valid, so use the cached data
            $data = file_get_contents($cacheFile);
        } else {
            // The cache is not valid, so fetch the new data and cache it

            // Fetch the feed data from the Instagram API
            $data = file_get_contents('https://api.instagram.com/v1/users/self/media/recent?access_token=' . $accessToken . '&count=' . $limit);

            // Check if the response from the API is an error
            if (!empty($data['error'])) {
                // The API returned an error, so use the cache data
                $data = file_get_contents($cacheFile);
            } else {
                // Cache the data
                file_put_contents($cacheFile, $data);
            }
        }

        // Decode the data into an array
        $data = json_decode($data, true);

        // Loop through the feed data and display each post
        foreach ($data['data'] as $post) {
            echo '<div class="item"><a href="' . $post['link'] . '" target="_blank"><img title="' . $post['caption']['text'] . '" src="' . $post['images']['standard_resolution']['url'] . '" /></a></div>';
        }
        ?>


    </div>
</section>