(function($) {
    $( document ).ready(function() {
        $(".li-grid-item").each(function() {
            const parrent = $(this);
            parrent.find('.tabs--js').on('click', function() {
                $(this)
                .addClass('active')
                .siblings().removeClass('active');
                parrent.find('.tabs__list > li')
                    .removeClass('active')
                    .eq($(this).index()).addClass('active');
            });
            parrent.find('.tabs--js a').on('click', function(e) {
                e.preventDefault();
            });
            });

        // const currentElement = document.getElementsByClassName('sub-services--item a active');
    // ../../../templates/dvbank/img/content/help.svg
        // currentElement.click(function(e) {
        //     e.preventDefault();
        // });

        /*
        var winWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
        if(winWidth >= 768) {
            $('.cabinet_remittance_form_2 input.form-control').after("<i title=\"Перевірте правільність вводу даних\" class='tips--helper'>\n" +
                "        <img src='../../../templates/dvbank/img/content/help.svg' class=\"icons icons--tips\">\n" +
                "    </i>")
        }
        */

    });

    $(window).on("load, resize",function(){
        var winWidth = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
        if(winWidth >= 768) {
            $(".custom__scroll--js").mCustomScrollbar({
                setHeight: 300,
                theme: "rounded"
            });
        }
    });

})(jQuery);