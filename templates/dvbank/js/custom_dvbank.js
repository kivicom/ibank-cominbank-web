
jQuery(document).ready(function () {

    (function ($) {
        $("body").on("click", function (e) {
            $('.button-with-options .list-with-options').addClass("hide");
        });

        $('.button-with-options').on('click', function (e) {
            let thisElem = $(this);
            setTimeout(function () {
                thisElem.find('.list-with-options').toggleClass('hide');
            }, 5);
        });

        $(".profile-block__title").click(function () {
            fixed_bottom();
            $(this).parents('.profile-block').find('.profile-container').slideToggle(200, function () {
                $(this).parents('.profile-block').toggleClass('profile-block-collapsed');
                fixed_bottom();
            });
        });
    })(jQuery);

});