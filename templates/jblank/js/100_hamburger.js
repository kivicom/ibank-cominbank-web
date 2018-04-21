jQuery.fn.extend({
    hamburger_uc: function(settings) {
        var element = jQuery("#"+this.attr("id"));
        var swipeZone = jQuery(settings.swipe_zone);
        element.css({
            height: '100%',
            position: 'fixed',
            top: 0,
            zIndex: 3
        });
        swipeZone.css({
            background: 'transparent',
            height: '100%',
            position: 'absolute',
            right: -50,
            top: 0,
            width: '100px'
        });

        var widthPanel;
        swipeZone.swipe({
            swipeStatus:function(event, phase, direction, distance, duration, fingers)
            {
                widthPanel = element.outerWidth();
                if (phase=="move" && direction =="right") {
                    element.animate({left:'0px'},widthPanel, function () {
                        element.addClass("opened");
                    });
                    return false;
                }
                if (phase=="move" && direction =="left") {
                    element.animate({left:'-'+widthPanel+'px'},widthPanel, function () {
                        element.removeClass("opened");
                    });
                    return false;
                }
            }
        });

        jQuery(settings.swipe_button).click(function () {
            widthPanel = element.width();
            if (element.hasClass("opened")){
                element.animate({left:'-'+widthPanel+'px'},widthPanel, function () {
                    element.removeClass("opened");
                });
            } else {
                element.animate({left:'0px'},widthPanel, function () {
                    element.addClass("opened");
                });
            }
        });
    }
});


