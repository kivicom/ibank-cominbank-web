<?php
$disabled = isset($disabled) ? $disabled : false;
$parameter = isset($parameter) ? $parameter: 'amountInCents';
$amountInCents = isset($data[$parameter]) ? preg_replace('/\D/', '', $data[$parameter]) : 0;
$currency = isset($currency) ? $currency : "UAH";
$label = isset($label) ? $label : "{CABINET-REMITTANCE-AMOUNT}";
$required = isset($required) ? $required : true;
$finance_type = isset($data['finance_type']) ? $data['finance_type'] : 0;
if (isset($data[$parameter]) && ($data[$parameter] === false)) {

} else {
    if(isset($_GET['AJAX'])){ ?>
        <div class="clearfix"></div>
        <div class="remittance_form_amount form-group row">
            <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
            <label class="col-md-3 col-sm-3 col-xs-4 text-left"><?= $label ?></label>

            <div class="col-md-8 col-sm-8 col-xs-6 text-left">
                <input class="<?= $required ? "required" : "" ?> form-control" data-mask="money"
                       name="data[<?= $parameter ?>]" type="text"
                       placeholder=""
                       value="<?= $tpl->priceFormat($amountInCents, false) ?>"
                    <?= $disabled ? 'disabled="disabled"' : "" ?>>

                <?php
                if ($disabled) echo '<input type="hidden" name="data[' . $parameter . ']" value="' . $amountInCents . '" />';
                ?>

                <span><?= $currency ?></span>
            </div>
            <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
        </div>
    <?php }else{
    ?>
    <div class="clearfix"></div>
    <div class="<?php if($finance_type != 3) { ?>remittance_form_amount<?php } ?> form-group row">
        <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
        <?php if($finance_type == 3 ) { ?>
            <label class="col-md-3 col-sm-3 col-xs-4 text-left"><?= $label ?></label>
        <?php } else { ?>
        <label class="col-md-4 col-sm-4 col-xs-4 text-right"><?= $label ?></label>
        <?php } ?>
        <div class="col-md-7 col-sm-7 col-xs-6 text-left">
            <input class="<?= $required ? "required" : "" ?> form-control" data-mask="money"
                   name="data[<?= $parameter ?>]" type="text"
                   placeholder=""
                   value="<?= $tpl->priceFormat($amountInCents, false) ?>"
                <?= $disabled ? 'disabled="disabled"' : "" ?>>

            <?php
            if ($disabled) echo '<input type="hidden" name="data[' . $parameter . ']" value="' . $amountInCents . '" />';
            ?>

            <span><?= $currency ?></span>
        </div>
        <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
    </div>

    <?php
    }
}
?>