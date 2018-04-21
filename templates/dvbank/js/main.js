


// (function ($) {
//
//     $( document ).ready(function() {
//         $(".edit-buttons_hidden").each( function(e) {
//
//             $(this).on('click', function(e) {
//                 $(this).hide();
//             });
//
//         });
//     });
// })(jQuery);
// ;
// (function ($) {
//
// })(jQuery);

(function ($) {
    function getSummLimits(parent, inputObjSum, inputObjTerm) {
        var summInput = $(parent).find(inputObjSum)[0];
        $(parent).on('keydown', function(e) {
            if (e.keyCode === 189){return false;}
            else {
                summInput.value = 0;
                $(parent).find(inputObjTerm).each( function (e) {
                    var termInput = Math.abs(parseInt($(this).val() , 10));
                    if (isNaN(termInput) === false) {
                        summInput.value = parseInt(summInput.value, 10) + termInput;
                    }
                });
            }
        });
        $(parent).trigger('keydown');
    };


})(jQuery);

// (function ($) {
//     $( document ).ready(function() {
//         $('.overview_payments .sub-services__list li').on('click', function(e) {
//             e.preventDefault();
//             $(this)
//                 .addClass('active')
//                 .siblings().removeClass('active');
//             $('.sub-services__descr')
//                 .removeClass('active')
//                 .eq($(this).index()).addClass('active');
//         });
//
//
//         $('.overview__inner .services__name').on('click', function() {
//             $(this).closest(".overview__inner").find(".sub-services").fadeToggle('sub-services');
//             $(this).closest(".overview__inner").find(".products").fadeToggle('products');
//             $(this).find(".icons").toggleClass('icons_chevron-blue-js');
//         });
//
//         var currentElement = $(".overview_payments .sub-services__list li a");
//         currentElement.click(function() {
//             currentElement.removeClass("current");
//             $(this).addClass("current");
//         });
//
//         currentElement.click(function(e) {
//             e.preventDefault();
//         });
//         var currentPaymentsTabMob = $(".overview_payments .sub-services__list li i");
//         currentElement.click(function() {
//             currentElement.removeClass("current-i");
//             $(this).addClass("current-i");
//         });
//
//         currentPaymentsTabMob.click(function(e) {
//             e.preventDefault();
//         });
//
//     });
// })(jQuery);





(function ($) {
    $( document ).ready(function() {
        $('.exchange-style').on('click', function(e) {
            $('.exchange-rate .exchange-tab').toggleClass('non-active')
        });
    });
})(jQuery);

// (function ($) {
//     $( document ).ready(function() {
//
//         let htmlHeight = $('html').outerHeight();
//         let displayHeight = $(window).height();
//
//
//         if (displayHeight > htmlHeight) {
//             let hf = $('header').outerHeight() + $('footer').outerHeight();
//
//             if ($('.content-sec').length) {
//                 $('.content-sec').height(displayHeight - hf);
//             } else {
//                 if ($('.login-sec').length) {
//                     $('.login-sec').height(displayHeight - hf);
//                 } else {
//                     if ($('.cabinet-registration').length) {
//                         $('.cabinet-registration').height(displayHeight - hf);
//                     }
//                 }
//             }
//         }
//     });
// })(jQuery);




(function ($) {
    $( document ).ready(function() {
        $('.map-head__list li').on('click', function(e) {
            e.preventDefault();
            $(this)
                .addClass('active')
                .siblings().removeClass('active');
            $('.map-toggle')
                .removeClass('non-active')
                .eq($(this).index()).addClass('non-active');
        });
    });
})(jQuery);

jQuery(document).ready(function() {
    (function ($) {
    $(".hint")
        .mouseover(function() {
            if($(window).width() - $( this ).offset().left  > 200){
                $( this ).append('<div class="text">'+$( this ).data("text")+'</div>')
                // console.log("hover");
            }else{
                $( this ).append('<div class="text right">'+$( this ).data("text")+'</div>')
            }
        })
        .mouseout(function() {
            $(this).find(".text").remove();
            // console.log("out");
        });

    if($('.user-operation-block .body').length) {
        $('.user-operation-block .body').perfectScrollbar();
    }
    if($('.user-products-block .body').length) {
        $('.user-products-block .body').perfectScrollbar();
    }

    /*show pass*/

    // /*transfer card*/
    // function mouseUpHandlerTransfer (e) {
    //     var container = $(".chose-item"),
    //         container2 = $(".chose-item *");
    //
    //     if (!container.is(e.target) && !container2.is(e.target)  ) // ... nor a descendant of the container
    //     {
    //         $(document).unbind('mouseup touchend', mouseUpHandlerTransfer); //Remove the event listener to prevent memory leaks
    //         $(".chose-item").removeClass("open");
    //     }
    // }
    //
    // $(".chose-item").click(function(){
    //     if( $(this).hasClass("open") ){
    //         $(this).removeClass("open");
    //     }else{
    //         $(this).addClass("open");
    //         $(document).bind('mouseup touchend', mouseUpHandlerTransfer);
    //     }
    // });

    /*transfer card*/

    $(".finance-calendar").click(function(){
        $(".finance-calendar-popup").show();
        console.log("Welcome");
    });
    $(".finance-calendar-popup .popup-close").click(function(){
        $(".finance-calendar-popup").hide();
    });
    })(jQuery);
});



(function ($) {
    $( document ).ready(function() {
        $('.operations__list li').on('click', function(e) {
            e.preventDefault();
            $(this)
                .addClass('active')
                .siblings().removeClass('active');
            $('.operations-table .tabs')
                .removeClass('active-table')
                .eq($(this).index()).addClass('active-table');
        });

        var currentElement = $(".operations-table .tabs active-table");

        currentElement.click(function(e) {
            e.preventDefault();
        });
    });
})(jQuery);



// ;
// (function ($) {
//     $( document ).ready(function() {
//
//     	var symbol_limit = 150;
//
//     	$(".client-input").each(function(){
//     	    var input_count = $(this).find(".input-count");
//             var tips_count = $(this).find(".tips-count");
//
//             tips_count.html("Залишилося символів " + symbol_limit);
//
//             input_count.keyup(function() {
//                 var symbol_length = $(this).val().length;
//                 var symbol_remaining = symbol_limit - symbol_length;
//                 tips_count.html("Залишилося символів " + symbol_remaining);
//
//                 if (symbol_length >= symbol_limit) {
//                     tips_count.addClass("tips_warning");
//                 } else {
//                     tips_count.removeClass("tips_warning");
//                 }
//             });
//     	});
//     });
// })(jQuery);


//# sourceMappingURL=main.js.map
