function rimitdish(opts) {
    $('#dish-external-frame').height($(window).height() - $('#dish-external-wrapper').height());
    $(window).resize(function() {
            $('#dish-external-frame').height($(window).height()
                                             - $('#dish-external-wrapper').height());
        });
}
