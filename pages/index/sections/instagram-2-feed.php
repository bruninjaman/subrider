<section id="subriderfeed" class="wrapper style2 special fade">
    <div class="container">
        <h2>Instagram Subrider</h2>
        <div id="instafeed" class="owl-carousel owl-theme owl-loaded owl-drag">
            <?php
            // // Define the access token
            // $accessToken = 'IGQVJYdTZAqVV9OcVRIcDI0NFRIa0FJLWI4LUpFR00yeG03eF9uQTctdVhqNXVyUDM5MDF1LWpJZAGgxRGlIdFlMTm9WSndNSmc4X25VUC1YdnlxZAEpKazhTSU5JMGJsSWlQYzdYV1Q5NW9wRjc2X3ozZAQZDZD';

            // // Define the number of posts to retrieve
            // $limit = 20;

            // // Define the cache file name
            // $cacheFile = 'instagram_feed_data.json';

            // // Check if the cached data is still valid
            // if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < 60 * 60 * 24)) {
            //     // The cache is still valid, so use the cached data
            //     $data = file_get_contents($cacheFile);
            // } else {
            //     // The cache is not valid, so fetch the new data and cache it

            //     // Fetch the feed data from the Instagram API
            //     $apiUrl = 'https://api.instagram.com/v1/users/self/media/recent?access_token=' . $accessToken . '&count=' . $limit;
            //     $ch = curl_init($apiUrl);
            //     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //     $data = curl_exec($ch);
            //     curl_close($ch);

            //     // Check if the response from the API is an error
            //     $json = json_decode($data, true);
            //     if (!empty($json['meta']['error_type'])) {
            //         // The API returned an error, so use the cache data
            //         $data = file_get_contents($cacheFile);
            //     } else {
            //         // Cache the data
            //         file_put_contents($cacheFile, $data);
            //     }
            // }

            // // Decode the data into an array
            // $data = json_decode($data, true);

            // // Loop through the feed data and display each post
            // foreach ($data['data'] as $post) {
            //     echo '<div class="item"><a href="' . $post['link'] . '" target="_blank"><img title="' . $post['caption']['text'] . '" src="' . $post['images']['standard_resolution']['url'] . '" /></a></div>';
            // }
            
            // Define the access token and Instagram account ID
            $accessToken = 'IGQVJYdTZAqVV9OcVRIcDI0NFRIa0FJLWI4LUpFR00yeG03eF9uQTctdVhqNXVyUDM5MDF1LWpJZAGgxRGlIdFlMTm9WSndNSmc4X25VUC1YdnlxZAEpKazhTSU5JMGJsSWlQYzdYV1Q5NW9wRjc2X3ozZAQZDZD';
            $accountId = '31236295';
            
            // Define the number of posts to retrieve
            $limit = 20;
            
            // Define the cache file name
            $cacheFile = 'instagram_feed_data.txt';
            
            // Check if the cached data is still valid
            if (file_exists($cacheFile) && (time() - filemtime($cacheFile) < 60 * 60 * 24)) {
                // The cache is still valid, so use the cached data
                $data = file_get_contents($cacheFile);
            } else {
                // The cache is not valid, so fetch the new data and cache it
            
                // Fetch the feed data from the Instagram Graph API
                $url = "https://graph.instagram.com/{$accountId}/media?fields=id,caption,media_type,media_url,thumbnail_url,permalink&access_token={$accessToken}&limit={$limit}";
                $data = file_get_contents($url);
            
                // Check if the response from the API is an error
                if (!empty(json_decode($data)->error)) {
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
                if ($post['media_type'] == 'VIDEO') {
                    $postUrl = $post['thumbnail_url'];
                } else {
                    $postUrl = $post['media_url'];
                }
                echo '<div class="item"><a href="' . $post['permalink'] . '" target="_blank"><img title="' . $post['caption'] . '" src="' . $postUrl . '" /></a></div>';
            }
            
            ?>
            <div>Em progresso</div>
        </div>
    </div>
</section>