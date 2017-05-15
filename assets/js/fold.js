(function($) {
    function setSickFrontHeight() {
        var height = $('#wpfooter').offset().top - $('#wpadminbar').offset().top - 100;
        $('.sickfront-wrapper').css('height', height + 'px');
        $('.sidebar-column').css('height', height + 'px');
        $('.preview-column').css('height', height + 'px');
    }

    $(document).ready(function() {
        $('body').addClass('folded');
        setSickFrontHeight();
    });

    $(window).resize(function() {
        setSickFrontHeight();
    });
})(jQuery);
