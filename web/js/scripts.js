(function ($, window) {
    /*
     document.ready
     ================================================================================
     */
    $(function() {
        $('[data-post-onclick]').click(function () {
            $.post($(this).data('post-onclick'));
        });
    });
}(jQuery, window));
