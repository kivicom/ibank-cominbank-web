<?php
$disabled = isset($disabled) ? $disabled : false;
$parameter = isset($parameter) ? $parameter: 'bankId';
$label = isset($label) ? $label: '';
$func = "MFO_".uniqid();
$input_bankname = isset($input_bankname) ? $input_bankname: '';
$finance_type = isset($data['finance_type']) ? $data['finance_type'] : 0;
if (isset($data[$parameter]) && ($data[$parameter] === false)) {

} else {
    ?>

    <div class="remittance_form_MFO form-group row">
        <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
        <label class="col-md-3 col-sm-3 col-xs-12 text-left"><?= $label ?></label>
        <div class="col-md-3 col-sm-3 col-xs-12">
            <input id="bankMFO" class="required form-control <?php echo $finance_type?'input-tips':'' ?>" name="data[<?= $parameter ?>]" type="text"
                   value="<?= isset($data[$parameter]) ? htmlentities($data[$parameter]) : "" ?>" <?= $disabled ? 'disabled="disabled"' : "" ?>
                   data-mask="mfo" onchange="<?= $func ?>(jQuery(this))">
            <?php if($finance_type) { ?>
            <i title="<?= $label ?>" class="tips--helper">
                <img src="../../../templates/dvbank/img/content/help.svg" class="icons icons--tips">
            </i>
            <?php }
            if ($disabled) echo '<input type="hidden" name="data[' . $parameter . ']" value="' . (isset($data[$parameter]) ? htmlentities($data[$parameter]) : "") . '" />';
            ?>

        </div>
        <label class="col-md-4 col-sm-4 col-xs-12 bankName text-left">
            <span>{BANK}: </span><span id="bankName"></span>
        </label>
        <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
    </div>

    <script>
        function <?=$func?>(elem) {
            var mfo = elem.val();
            var autocomplete = 'bankName';
            (function ($) {
                if (autocomplete !== false) jQuery("#" + autocomplete).prop("disabled", true);
                $.ajax({
                    url: '/index.php?AJAX&TEMPLATE=getBankByMFO&mfo=' + mfo,
                    method: "post",
                    dataType: "json",
                    success: function (r) {
                        if ((r !== null) && (typeof r === 'object')) {
                            elem.removeClass("error");
                            if (autocomplete !== false) {
                                jQuery("#" + autocomplete).text(r.request);
                            }
                        } else elem.addClass("error");
                    }
                });
            }(jQuery));
        }
        <?php if(!empty($data[$parameter])){ ?>
            <?=$func?>(jQuery('#bankMFO'));
        <?php } ?>
    </script>
<?php
}
?>