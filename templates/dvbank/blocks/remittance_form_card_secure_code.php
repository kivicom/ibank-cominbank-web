<div class="remittance_form_card_secure_code form-group col-lg-5 col-md-5 col-sm-12 col-xs-12">
    <label>{CABINET-REMITTANCE-SECURECODE}</label>
    <input type="hidden" class="required true-secure-code" name="data[secureCode]" data-mask='secureCode' />
    <input class="form-control not-eye secure-code-input pseudo-input" type="tel" data-mask='secureCode' autocomplete="off">
</div>
<script type="text/javascript">
    jQuery(document).ready(function () {
        (function($) {
            $('.secure-code-input')
                .keydown(function(e) {
                    if(e.key.length == 1) {
                        if(e.key == 'Backspace') {
                            $('.true-secure-code').val($('.true-secure-code').val().substr(0, $('.true-secure-code').val().length - 1));
                        } else {
                            var maxlength = 3;
                            e.preventDefault();
                            if(/^\d+$/.test(e.key)){
                                $('.true-secure-code').val(($('.true-secure-code').val() + e.key).substr(0,maxlength));
                                $(this).val(($(this).val() + '•').substr(0,maxlength));
                            }
                        }
                    }
                })
                .keyup(function(e) {
                    if(e.key == 'Backspace' && !$(this).val().length) {
                        $('.true-secure-code').val('');
                    }
                });
        })(jQuery);
    });
</script>