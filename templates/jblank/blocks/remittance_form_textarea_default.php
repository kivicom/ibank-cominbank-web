<?php
$id = isset($id) ? $id : "textarea_".uniqid();

$func = "textarea".uniqid();
$disabled = isset($disabled) ? $disabled : false;
$parameter = isset($parameter) ? $parameter : 'accountNumber';
$label = isset($label) ? $label : '';
$maxlength = isset($maxlength) ? $maxlength : false;
$maxlength = $disabled ? false : $maxlength;
$required = isset($required) ? $required : true;

if (isset($data[$parameter]) && ($data[$parameter] === false)) {

} else {
    ?>
    <div class="remittance_form_textarea_default form-group row">
        <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
        <label class="col-md-3 col-sm-3 col-xs-12 text-left"><?= $label ?></label>
        <div class="col-md-7 col-sm-7 col-xs-12 text-left">
        <textarea id="<?= $id ?>"
                  class="<?= $required ? "required" : "" ?> input-count form-control"
                  name="data[<?= $parameter ?>]"
                  placeholder=""
            <?= $disabled ? 'disabled="disabled"' : "" ?>
            <?= $maxlength ? ('
                data-maxlength="' . $maxlength . '"
                onkeyup="' . $func . '()"
            ') : "" ?>
        ><?= isset($data[$parameter]) ? $data[$parameter] : "" ?></textarea>
            <?php
            if ($disabled) echo '<input type="hidden" name="data[' . $parameter . ']" value="' . (isset($data[$parameter]) ? htmlentities($data[$parameter]) : "") . '" />';

            if ($maxlength) {
                ?>
                <div class="row col-md-12">
                    {TEXTAREA-LEFT-SYMBOLS}: <span class="textarea-count"></span>
                </div>
                <?php
            }
            ?>
        </div>
        <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
    </div>

    <?php
    if ($maxlength) {
        ?>
        <script>
            function <?=$func?>() {
                (function ($) {
                    var input_count = $("#<?=$id?>");
                    var symbol_limit = input_count.data("maxlength");
                    var tips_count = input_count.closest(".form-group").find(".textarea-count");
                    var symbol_length = input_count.val().length;
                    var symbol_remaining = symbol_limit - symbol_length;
                    tips_count.html((symbol_remaining >= 0) ? symbol_remaining : 0);

                    if (symbol_length >= symbol_limit) {
                        input_count.val(input_count.val().substring(0, <?=$maxlength?>));
                    }
                })(jQuery);
            }

            <?=$func?>();
        </script>
        <?php
    }
}
?>

