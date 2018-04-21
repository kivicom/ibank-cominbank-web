// check and define $ as jQuery

var overflow = 0;

if (typeof jQuery != "undefined") jQuery(function ($) {

    // dump(myVar); is wrapper for console.log() with check existing console object and show 
    window.dump=function(vars,name,showTrace){if(typeof console=="undefined")return false;if(typeof vars=="string"||typeof vars=="array")var type=" ("+typeof vars+", "+vars.length+")";else var type=" ("+typeof vars+")";if(typeof vars=="string")vars='"'+vars+'"';if(typeof name=="undefined")name="..."+type+" = ";else name+=type+" = ";if(typeof showTrace=="undefined")showTrace=false;console.log(name,vars);if(showTrace)console.trace();return true};

    // remove no-js class if JavaScript enabled
    $('html.no-js').removeClass('no-js').addClass('js-ready');

    // close Joomla system messages (just example)
    $('#system-message .close').click(function () {
        $(this).closest('.alert').animate({height: 0, opacity: 0, MarginBottom: 0}, 'slow', function () {
            $(this).remove();
        });
        return false;
    });
});

var service_information;


jQuery(document).ready(function () {

    service_information = jQuery("#service_information");

    (function($) {

        $.fn.zoomToSmallerFooter = function(pos, defaultWidth, breakpoint) {
            if (breakpoint === undefined){
                breakpoint = 768;
            }

            var actualWidth = parseInt($(window).width());
            var headerHeight = $('.header').outerHeight();

            if (breakpoint < actualWidth) {
                if (defaultWidth === undefined) {
                    defaultWidth = 1920;
                }
                var actualWidth = parseInt($(window).width());
                var proportion = (actualWidth / parseInt(defaultWidth)).toFixed(10);
                var thisWidth = $(this).width();
                var thisHeight = $(this).height();
                var left = (100 - 100/proportion)/2 + "%";
                $(this).css('transform-origin', pos);
                $(this).css('transform',  'scale('+proportion+','+proportion+')');
                $(this).css('-moz-transform',  'scale('+proportion+','+proportion+')');
                $(this).css('width', 100/proportion + "%");

                $('#mobile-nav').css('top', headerHeight * proportion);

                return this;
            }else{
                $(this).css('transform-origin', "none");
                $(this).css('transform',  'none');
                $(this).css('-moz-transform',  'none');
                $(this).css('width', 100+ "%");

                $('#mobile-nav').css('top', headerHeight);

                return this;
            }
        };

        // вызов соответсвующих функций для пропорционального уменьшения ...


        $(window).resize(function() {
            $('.footer-zoom-js').zoomToSmallerFooter("0% 100%");
            $('.header-zoom-js').zoomToSmallerFooter("0% 0%");
            fixed_bottom();
        });
        $(window).resize();


        init();
        fixed_bottom();

        //Auto open modal form GET

        var scroll = service_information.data("scroll");
        if (scroll) {
            $('body,html').scrollTop(parseInt(scroll) - 1);
        }


        $("#sidebar_mobile").hamburger_uc({
            swipe_zone: ".sidebar_mobile_swipe",
            swipe_button: ".toggle_sidebar"
        });

        jQuery("body").on("click", "[data-tab_for]", function () {
            var id = $(this).data("tab_for");
            $("[data-tab_for]").removeClass("active");
            $("[data-tab_for='"+id+"']").addClass("active");
            fixed_bottom();
        });

        $('[data-toggle="tooltip"]').tooltip();

        $('html').click(function(e) {
            $("[data-toggle='popover']").popover('hide');
        });

        $("[data-toggle='popover']").popover({
            html: true,
            trigger: 'manual'
        }).click(function(e) {
            $(this).popover('toggle');
            e.stopPropagation();
        });

        $(".ajax").each(function () {
            var template = $(this).data("template");
            var url = $(this).data("url");
        });

        //Раскрывающийся блок
        $(".js-slide_toggle").each(function() {
            var toggle = $(this);
            toggle.find(".js-slide_toggle__event").click(function () {
                toggle.find(".js-slide_toggle__block").slideToggle(function () {
                    fixed_bottom();
                });
                toggle.toggleClass("opened");
            });
        });

        //Фильтры
        var filters = $(".filters");
        filters.find("input[type='radio']").change(function () {
            $(this).closest(".filters__radio").find(".form-group").removeClass("active");
            $(this).closest(".form-group").addClass("active");
        });
        filters.find("input[type='radio']:checked").change();
        filters.find(".reload").change(function () {
            var tab = $(".operations__list [data-tab_for].active").data("tab_for");
            var scroll = $("body").scrollTop();
            filters.append("<input type='hidden' name='scroll' value='"+scroll+"' />")
            filters.append("<input type='hidden' name='tab' value='"+tab+"' />")
            filters.submit();
        });

        initPasswordsInputs();

        $(".card-limits").each( function(e) {

            $(this).find('.change-limits-button').on('click', function(e) {
                $(this).find('.limits-change').toggleClass('limits-change_uneditable');
                $(this).closest(".card-limits").toggleClass('card-limits_uneditable');
            });

            $(this).on('mousedown', function(e) {
                // getSummLimits(this,".limits-input-sum1", ".limits-input-term1");
                // getSummLimits(this,".limits-input-sum2", ".limits-input-term2");
            });
        });
    })(jQuery);
});

function getSummLimits(parent, inputObjSum, inputObjTerm) {
    (function ($) {
        var summInput = $(parent).find(inputObjSum+":first");

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
    }(jQuery));
};

function validation(form) {
    if (form.target !== undefined) {
        var getForm = jQuery(form.target);
        getForm.find(".required").each(function () {
            var elem = jQuery(this);

            if (elem.attr("type") == "checkbox") {
                if (!elem.prop("checked")) {
                    addError(elem);
                } else {
                    elem.removeClass("error");
                }
            } else if (elem.attr("data-mask") == "phone") {
                var getVal = elem.val().replace(/[^0-9+]/g, "");
                if (getVal.length != 13) {
                    addError(elem);
                } else {
                    elem.removeClass("error");
                }
            } else if (elem.attr("data-mask") == "card_number") {
                var getVal = elem.val().replace(/[^0-9+]/g, "");
                if (getVal.length != 16) {
                    addError(elem);
                } else {
                    elem.removeClass("error");
                }
            } else if (elem.attr("data-mask") == "secureCode") {
                var getVal = elem.val().replace(/[^0-9+]/g, "");
                if (getVal.length != 3) {
                    addError(elem);
                } else {
                    elem.removeClass("error");
                }
            } else if (elem.attr("data-mask") == "money") {
                var getVal = parseInt(elem.val().replace(/[^0-9+]/g, ""));
                if (!getVal) {
                    addError(elem);
                } else {
                    elem.removeClass("error");
                }
            } else if (elem.attr("type") == "email") {
                if(/^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/.test(elem.val())
                    && !(/[^\x20-\x7f]/.test(elem.val()))){
                    elem.removeClass("error");
                }else{
                    addError(elem);
                }

            } else if (elem.attr("data-password") == "repeat") {
                var repeatVal = elem.val();
                var passwordVal = getForm.find("[data-password='password']").val();
                if(repeatVal == passwordVal && repeatVal !== ''){
                    elem.removeClass("error");
                }else{
                    addError(elem);
                }
            } else if (elem.attr("data-type") == "date") {
                var block = elem.data("block");
                var monthElem = getForm.find("[data-type='date'][data-block='"+block+"'][data-tag='month']");
                var yearElem = getForm.find("[data-type='date'][data-block='"+block+"'][data-tag='year']");

                var month = parseInt(monthElem.val());
                var year = parseInt(yearElem.val());

                month += 1;
                if (month > 12) {
                    month = 1;
                    year += 1;
                }

                function toTimestamp(year,month,day,hour,minute,second){
                    var datum = new Date(Date.UTC(year,month-1,day,hour,minute,second));
                    return datum.getTime()/1000;
                }

                var Timestamp = toTimestamp(year,month,1,0,0,0);
                var currentDateTime = Date.now();
                var currentTimestamp = Math.floor(currentDateTime / 1000);

                if (currentTimestamp >  Timestamp) {
                    addError(monthElem);
                    addError(yearElem);
                } else {
                    monthElem.removeClass("error");
                    yearElem.removeClass("error");
                }
            } else {
                if (!elem.val()) {
                    addError(elem);

                    //External auth required. Show modal
                    if (elem.hasClass("system_ext_auth_required") || elem.attr("id") == "system_ext_auth_required") {
                        elem.removeClass("error");
                        var errors = getForm.find(".error").length;
                        elem.addClass("error");
                        if (!errors) {
                            system_ext_auth_required_modal(getForm);
                        }
                    }
                }
                else {
                    if (elem.attr("data-mask") != "mfo"){
                        elem.removeClass("error");
                    }
                }
            }
        });

        getForm.find(".hidden .error").removeClass("error");

        if (getForm.find(".error:first").length) {
            jQuery("html, body").stop().animate({scrollTop:getForm.find(".error:first").offset().top - 150}, 500);
            getForm.find(".error:first").focus();
            getForm.find(".error:first + .pseudo-input").focus();

        }

        if (!getForm.find(".error").length) {
            return true;
        }
    }

    return false;
}

function addError(elem) {
    elem.addClass("error");
    setTimeout(function () {
        elem.removeClass("error");
    }, 800);
}

//Форма двухэтапной авторизации (модалка)
function system_ext_auth_required_modal(getForm) {
    (function ($) {
        var modal = jQuery("#system_ext_auth_required_modal");
        var modalButton = modal.find(".auth_button_submit");
        modal.modal();

        if(!getForm.find('.system_ext_auth_required').hasClass('no_auth_request')){
            $.ajax({
                url: '/index.php?AJAX&TEMPLATE=modal_ext_auth&method=requestAuthentication',
                method: "post",
                dataType: "json",
                success: function(r){
                    if (r.request !== undefined) {
                        if (r.request == "success") {
                            setTimeout(function () {
                                modal.find(".preloader_block").addClass("preloader_complete");
                                modal.find("input[name='otp']").focus();
                            }, 300);
                        } else oops();
                    } else oops();
                }
            });
        }else{
            setTimeout(function () {
                modal.find(".preloader_block").addClass("preloader_complete");
                modal.find("input[name='otp']").focus();
            }, 300);
        }

        modalButton.unbind();
        modalButton.click(function () {
            modal.find(".preloader_block").removeClass("preloader_complete");
            var OTP = modal.find("input[name='otp']").val();
            OTP = OTP.trim();

            var confirm = getForm.find('.system_ext_auth_required').data("confirm");
            confirm = (confirm !== undefined) ? confirm : "";

            if (confirm !== false) {
                $.ajax({
                    url: '/index.php?AJAX&TEMPLATE=modal_ext_auth&method=authenticate',
                    method: "post",
                    dataType: "json",
                    data: {otp: OTP},
                    success: function (r) {
                        if (r.request !== undefined) {
                            if (r.request == "success") {
                                setTimeout(function () {
                                    modal.find(".preloader_block").addClass("preloader_complete");
                                    $("#system_ext_auth_required_modal").modal("hide");
                                    getForm.find('.system_ext_auth_required').val(OTP);
                                    getForm.find('.system_ext_auth_required').closest("form").submit();
                                }, 300);
                            } else {
                                if (r.exception !== undefined) {
                                    if (r.exception == "wrong_otp_credentials") {
                                        modal.find(".preloader_block").addClass("preloader_complete");
                                        modal.find("input[name='otp']").addClass("error");
                                    } else if (r.exception == "password_attempts_exceeded") {
                                        modal.find(".preloader_block").addClass("preloader_complete");
                                        modal.modal('hide');
                                        $("#system_ext_auth_required_modal_overflow").modal();
                                    } else oops();
                                } else oops();
                            }
                        } else oops();
                    }
                });
            } else {
                if (OTP != "") {
                    modal.find("input[name='otp']").removeClass("error");
                    setTimeout(function () {
                        modal.find(".preloader_block").addClass("preloader_complete");
                        $("#system_ext_auth_required_modal").modal("hide");
                        getForm.find('.system_ext_auth_required').val(OTP);
                        getForm.find('.system_ext_auth_required').closest("form").submit();
                    }, 300);
                } else {
                    modal.find("input[name='otp']").addClass("error");
                    modal.find(".preloader_block").addClass("preloader_complete");
                }


            }
        });

        modal.find("input[name='otp']").unbind();
        modal.find("input[name='otp']").keydown(function (k) {
            if (k.keyCode == 13) {
                modalButton.click();
            }
        });
    }(jQuery));
}


//Форма сохранения, обновления и удаления шаблона,
function system_template_modal(elem) {
    (function ($) {
        var modal = $("#system_new_template_modal");
        var formData = elem.data("form");
        var template = elem.data("template");
        var finance_type = elem.data("finance_type");
        var operation_id = elem.data("operation_id");

        var data = {};
        var title = "";
        if (formData !== undefined) {
            data = $("#"+formData).serialize();
            data += "&SERVICE=new_template";
            title = "form";
        } else if(template !== undefined) {
            data = {templateId: template, SERVICE: "edit_template"};
            title = "template";
        }else if(operation_id !== undefined){
            data = {operationId: operation_id, SERVICE: "new_template"};
            title = "form";
        }

        modal.find(".modal-title").html(modal.find(".modal-title").data("title_"+title));

        modal.modal();

        modal.find(".preloader_block").removeClass("preloader_complete");
        $.ajax({
            url: '/index.php?AJAX&TEMPLATE=cabinet_remittance_template&FinancialOperationType=' + finance_type,
            method: "post",
            dataType: "html",
            data: data,
            success: function(r){
                modal.find(".preloader_content").html(r);
                init();
                setTimeout(function () {
                    modal.find(".preloader_block").addClass("preloader_complete");
                }, 300);

                var formNewTemplate = modal.find("form");

                formNewTemplate.unbind();
                formNewTemplate.submit(function (event) {
                    if (validation(event)) {
                        modal.find(".preloader_block").removeClass("preloader_complete");
                        $.ajax({
                            url: '/index.php?AJAX&TEMPLATE=cabinet_remittance&FinancialOperationType=' + finance_type,
                            method: "post",
                            dataType: "json",
                            data: formNewTemplate.serialize(),
                            success: function(r){
                                if (r.request !== undefined) {
                                    if (r.request == "success") {
                                        modal.find(".preloader_block").addClass("preloader_complete");
                                        modal.modal("hide");
                                        setTimeout(function () {
                                            $("#system_"+r.SERVICE+"_complete_modal").modal();
                                        }, 300);
                                    } else oops();
                                } else oops();
                            }
                        });
                    }
                    return false;
                });
            }
        });
    }(jQuery));
}


//Переход на страницу неопределенной ошибки
function oops() {
    location.href = "/cabinet/oops";
}

//Инициализация плагинов
function init() {
    (function($){
        $(".validate").each(function () {
            if (!$(this).hasClass("init-complete")) {
                $(this).addClass("init-complete");
            }

            var form = $(this);
            form.submit(validation);
        });



        //paletteColorPicker
        $('.paletteColorPicker').each(function(){
            if (!$(this).hasClass("init-complete")) {
                $(this).paletteColorPicker({
                    position: 'downside'
                });
                $(this).addClass("init-complete");
            }
        });

        //IconSelect
        $('.IconSelect').each(function () {
            if (!$(this).hasClass("init-complete")) {
                var el = $(this);
                var id = el.attr("id");

                var iconSelect = new IconSelect(id, {
                    'selectedIconWidth':el.data("selected_icon_width"),
                    'selectedIconHeight':el.data("selected_icon_height"),
                    'selectedBoxPadding':el.data("selected_box_padding"),
                    'iconsWidth':el.data("icons_width"),
                    'iconsHeight':el.data("icons_height"),
                    'boxIconSpace':el.data("box_icon_space"),
                    'vectoralIconNumber':el.data("vectoral_icon_number"),
                    'horizontalIconNumber':el.data("horizontal_icon_number")
                });

                var icons = [];

                for(var index=1; index<100; index++) {
                    var img = el.data("img_"+index);
                    var val = el.data("val_"+index);
                    if (img && val){
                        icons.push({'iconFilePath':img, 'iconValue':val});
                        continue;
                    }
                    break;
                }

                iconSelect.refresh(icons);

                el.unbind();

                el.find(".box img").click(function () {
                    setTimeout(function () {
                        el.attr("data-value", iconSelect.getSelectedValue());
                    },100);

                });

                if (el.data("value")) {
                    el.find(".box img[icon-value='"+el.data("value")+"']").click();
                } else {
                    el.find(".box img:first").click();
                }

                el.addClass("init-complete");
            }
        });

        //datepicker
        $('.datepicker').each(function(){
            $(this).datepicker({
                format: "dd.mm.yyyy",
                language: (service_information.data("lang") !== undefined) ? service_information.data("lang") : "uk"
            });
        });

        //money
        $("[data-mask='digits']").each(function(){
            $(this).mask('00000000000000000000');
        });

        $("[data-mask='money']").each(function(){
            $(this).maskMoney({thousands:' ', decimal:'.', allowZero:true});
            $("[data-mask='money']").maskMoney('mask');
        });

        $("[data-mask='card_number']").each(function(){
            $(this).mask('0000 0000 0000 0000');
        });

        $("[data-mask='secureCode']").each(function(){
            $(this).mask('000');
        });

        $("[data-mask='mfo']").each(function(){
            $(this).mask('000000');
        });

        $("[data-mask='phone']").each(function(){
            var input = $(this);
            var prefix = "+38";

            input.val(input.val() ? input.val() : prefix);

            input.unbind();
            input.on('input',function(){
                var str = input.val();
                if(str.indexOf(prefix) == 0) {
                    // string already started with prefix
                    return;
                } else {
                    if (prefix.indexOf(str) >= 0) {
                        // string is part of prefix
                        input.val(prefix);
                    } else {
                        input.val(prefix+str);
                    }
                }
            });

            $(this).mask('+38 (999) 999 99 99');
            $(this).keydown(function (e) {
                // Allow: backspace, delete, tab, escape, enter
                if ($.inArray(e.keyCode, [46, 8, 9, 27, 13]) !== -1 ||
                    // Allow: Ctrl+A, Command+A
                    (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
                    // Allow: home, end, left, right, down, up
                    (e.keyCode >= 35 && e.keyCode <= 40)) {
                    // var it happen, don't do anything
                    return;
                }
                // Ensure that it is a number and stop the keypress
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            });
        });

        SVGElementsInit("js-template-icons", function (Objects) {
            Array.prototype.filter.call(Objects, function(Object, i) {
                var color = Object.dataset.color;
                if (color) {
                    var svg = $(Object.contentDocument);
                    svg.find("g").css("fill", color);
                }
            });
        });
    }(jQuery))
}

function fixed_bottom() {
    //Footer to bottom
    jQuery("footer.footer").removeClass("fixed_bottom");
    var heightWindow = jQuery(window).height();
    var heightBody = jQuery("body").height();

    if (heightWindow > heightBody) {
        jQuery("footer.footer").addClass("fixed_bottom");
    } else {
        jQuery("footer.footer").removeClass("fixed_bottom");
    }

}


function SVGElementsInit(selector, func) {
    (function ($) {
        var Objects = [];
        var getObjects = document.getElementsByClassName(selector);
        Array.prototype.filter.call(getObjects, function(Object){
            var typeObj = Object.getAttribute("type");
            if (Object.getAttribute("type").indexOf("svg") != -1) Objects.push(Object);
        });

        var objectTimer = setInterval(function () {
            overflow++;
            if (overflow > 100) clearInterval(objectTimer);

            if (!$("."+selector+":not(.complete)").length){
                clearInterval(objectTimer);
                func(Objects);
            } else {
                Array.prototype.filter.call(Objects, function(Object){
                    var svg = $(Object.contentDocument);

                    if (svg.find("path").length) {
                        Object.classList.add("complete");
                    }
                });
            }
        },100);
    }(jQuery));
}

function initPasswordsInputs(){
    (function ($) {
        $("input[type='password']:not(.not-eye)").each(function (index) {
            $(this).addClass('view_password_'+index);
            var left = $(this).position().left;
            var top = $(this).position().top;
            var width = $(this).outerWidth(true);
            var height = $(this).outerHeight(true);

            $("#view_password_"+index).remove();

            // $(this).after('<span id="view_password_'+index+'" class="glyphicon glyphicon-eye-open" style="position: absolute; cursor: pointer; left:'+(left + width - 25)+'px; top:'+(top + height / 2 - 5)+'px"></span>');
            $(this).after('<span id="view_password_'+index+'" class="glyphicon glyphicon-eye-open" style="position: absolute; cursor: pointer; right:10px; top:'+(top + height / 2 - 6)+'px"></span>');
            $('#view_password_'+index).click(function () {
                if ($(this).hasClass("glyphicon-eye-open")) {
                    $('.view_password_'+index).attr("type", "text");
                    $(this).removeClass("glyphicon-eye-open");
                    $(this).addClass("glyphicon-eye-close");
                } else {
                    $('.view_password_'+index).attr("type", "password");
                    $(this).removeClass("glyphicon-eye-close");
                    $(this).addClass("glyphicon-eye-open");
                }
            });
        });
    }(jQuery));
}

// function updateGlyphicon() {
//     (function ($) {
//         $(".modal-body input[type='password']:not(.not-eye)").each(function (index) {
//             $(this).addClass('.modal-body view_password_'+index);
//             var left = $(this).position().left;
//             var top = $(this).position().top;
//             var width = $(this).outerWidth(true);
//             var height = $(this).outerHeight(true);
//
//             $("#view_password_"+index).remove();
//
//             $(this).after('<span id="view_password_'+index+'" class="glyphicon glyphicon-eye-open" style="position: absolute; cursor: pointer; right: 0; top:34px"></span>');
//         });
//
//     }(jQuery));
// }