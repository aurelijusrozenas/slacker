(function ($, window) {
    /*
     document.ready
     ================================================================================
     */
    $(function() {
        // needed for loader
        var $loader = $('.loader[data-url]');
        if ($loader.length == 0) {
            $('body').addClass('loaded');
        } else {
            $.get($loader.data('url'), function (data) {
                $loader.html(data);
                $('body').addClass('loaded');
            });
        }
        // post to url on click
        $('[data-post-onclick]').click(function () {
            $.post($(this).data('post-onclick'));
        });
    });
}(jQuery, window));
