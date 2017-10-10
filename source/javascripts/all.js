//= require ./all_nosearch
//= require ./app/_search

jQuery(function() {

    var toolbox = $('.tocify-wrapper'),
        height = toolbox.height();

    toolbox.bind('wheel', function (e) {
        var d  = e.originalEvent.deltaY,
            scrollHeight = toolbox.get(0).scrollHeight;
        if((this.scrollTop === (scrollHeight - height) && d > 0) || (this.scrollTop === 0 && d < 0)) {
            e.preventDefault();
        }
    });

});