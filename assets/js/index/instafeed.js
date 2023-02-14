var feed = new Instafeed({
    accessToken: 'IGQVJVcVBrMm8tNWdaV1VzcEN4bDZASYUpYbTBIeERMZAkZASV3VQNHA5SDM3X1BZANmpONkdnd0lDRmlHSk41YlFnTjVtWHpURml4aV9wRGN6TnN5QlNzQ3VGc19uYmdmU3RZAaWxMcW80ZAVlqak5WZAE9xUQZDZD',
    limit: 21,
    template: '<div class="item"><a href="{{link}}" target="_blank"><img title="{{caption}}" src="{{image}}" /></a></div>',
    after: function () {
        $(".owl-carousel").owlCarousel();
    },
    success: function (data) {
        // Check if there is already feed data stored in local storage
        if (localStorage.getItem("instagramFeedData") === null) {
            // If there isn't, store the feed data in local storage
            localStorage.setItem("instagramFeedData", JSON.stringify(data));
        } else {
            // If there is, retrieve the stored data from local storage and update the feed
            data = JSON.parse(localStorage.getItem("instagramFeedData"));
            feed.parse(data);
        }
    }
});
feed.run();