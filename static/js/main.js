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







//# sourceMappingURL=main.js.map
