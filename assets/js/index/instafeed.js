var feed = new Instafeed({
    accessToken: 'IGQVJYV25ROUFndzlXQWlkMDd0VDJmU2lLWW9YZADI2RzM0QW1NQjhBSU9ubTEtLUtyT21xSnhsWG5vbWh0U1BtSzcyODhQemtTN1NyM0tpTlpUUS15ckZAKR051emVRWjM1ckdCWS1UOWhWZAmpQUHJJXwZDZD',
    limit: 21,
    template: '<div class="item"><a href="{{link}}" target="_blank"><img title="{{caption}}" src="{{image}}" /></a></div>',
    after: function () {
        $(".owl-carousel").owlCarousel();
    }
});
feed.run();