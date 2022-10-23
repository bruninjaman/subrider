var feed = new Instafeed({
    accessToken: 'IGQVJXaU1pRVBJUHZAwZAS03N0dCbzZAJT2ZA6OERDM2txMC1vdlNUbEtxeGR6ZAENqaHY5c3ZAab3RzMUNIWnNDMEdDbmttbEJYQkZAqeUp4VDR1eHROczZA4T1d1VW5VQWpVQ2VyLXRJSWxUdGNpajVJUG14dwZDZD',
    limit: 21,
    template: '<div class="item"><a href="{{link}}" target="_blank"><img title="{{caption}}" src="{{image}}" /></a></div>',
    after: function () {
        $(".owl-carousel").owlCarousel();
    }
});
feed.run();