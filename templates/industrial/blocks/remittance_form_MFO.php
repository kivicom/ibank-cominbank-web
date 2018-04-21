<?php
$disabled = isset($disabled) ? $disabled : false;
$parameter = isset($parameter) ? $parameter: 'bankId';
$label = isset($label) ? $label: '';
$func = "MFO_".uniqid();
$input_bankname = isset($input_bankname) ? $input_bankname: '';
if (isset($data[$parameter]) && ($data[$parameter] === false)) {

} else {
    ?>

    <div class="remittance_form_MFO form-group row">
        <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
        <label class="col-md-3 col-sm-3 col-xs-12 text-left"><?= $label ?></label>
        <div class="col-md-7 col-sm-7 col-xs-12">
            <input class="required form-control" name="data[<?= $parameter ?>]" type="text"
                   value="<?= isset($data[$parameter]) ? htmlentities($data[$parameter]) : "" ?>" <?= $disabled ? 'disabled="disabled"' : "" ?>
                   data-mask="mfo" onchange="<?= $func ?>(jQuery(this))">
            <?php
            if ($disabled) echo '<input type="hidden" name="data[' . $parameter . ']" value="' . (isset($data[$parameter]) ? htmlentities($data[$parameter]) : "") . '" />';
            ?>

        </div>
        <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
    </div>

    <script>
        function <?=$func?>(elem) {
            var mfo = elem.val();
            var autocomplete = <?=$input_bankname ? ("'" . $input_bankname . "'") : "false" ?>;
            (function ($) {
                if (autocomplete !== false) jQuery("#" + autocomplete).prop("disabled", true);


                $.ajax({
                    url: '/index.php?AJAX&TEMPLATE=getBankByMFO&mfo=' + mfo,
                    method: "post",
                    dataType: "json",
                    success: function (r) {
                        jQuery("#" + autocomplete).prop("disabled", false);
                        if ((r !== null) && (typeof r === 'object')) {
                            elem.removeClass("error");
                            if (autocomplete !== false) {
                                jQuery("#" + autocomplete).val(r.request);
                            }
                        } else elem.addClass("error");
                    }
                });
            }(jQuery));
        }
    </script>
<?php
}
?>