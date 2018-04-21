// ;
// (function ($) {
//
// })(jQuery);

// (function ($) {
//     function getSummLimits(parent, inputObjSum, inputObjTerm) {
//         var summInput = $(parent).find(inputObjSum)[0];
//         $(parent).on('keydown', function(e) {
//             if (e.keyCode === 189){return false;}
//             else {
//                 summInput.value = 0;
//                 $(parent).find(inputObjTerm).each( function (e) {
//                     var termInput = Math.abs(parseInt($(this).val() , 10));
//                     if (isNaN(termInput) === false) {
//                         summInput.value = parseInt(summInput.value, 10) + termInput;
//                     }
//                 });
//             }
//         });
//         $(parent).trigger('keydown');
//     };
//
//     $( document ).ready(function() {
//         $(".card-limits").each( function(e) {
//
//             $(this).find('.change-limits-button').on('click', function(e) {
//                 $(this).find('.limits-change').toggleClass('limits-change_uneditable');
//                 $(this).closest(".card-limits").toggleClass('card-limits_uneditable');
//             });
//
//             $(this).on('mousedown', function(e) {
//                 getSummLimits(this,".limits-input-sum1", ".limits-input-term1");
//                 getSummLimits(this,".limits-input-sum2", ".limits-input-term2");
//             });
//         });
//     });
// })(jQuery);

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
(function ($) {
    $( document ).ready(function() {
        $('.exchange-style').on('click', function(e) {
            $('.exchange-rate .exchange-tab').toggleClass('non-active')
        });
    });
})(jQuery);





// (function ($) {
//     $( document ).ready(function() {
//         $('.map-head__list li').on('click', function(e) {
//             e.preventDefault();
//             $(this)
//                 .addClass('active')
//                 .siblings().removeClass('active');
//             $('.map-toggle')
//                 .removeClass('non-active')
//                 .eq($(this).index()).addClass('non-active');
//         });
//     });
// })(jQuery);



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
