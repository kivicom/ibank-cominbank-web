<?php
$disabled = isset($disabled) ? $disabled : false;
$parameter = isset($parameter) ? $parameter: 'cardNumber';
$label = isset($label) ? $label: '';
?>

<div class="remittance_form_CARDNUM form-group row">
    <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
    <label class="col-md-3 col-sm-3 col-xs-12 text-left"><?=$label?></label>
    <div class="col-md-7 col-sm-7 col-xs-12">
        <input class="required form-control" name="data[<?=$parameter?>]" type="text" placeholder="0000 0000 0000 0000" value="<?=isset($data[$parameter]) ? $data[$parameter] : ""?>" <?=$disabled ? 'disabled="disabled"' : "data-mask='card_number'"?>>
        <?php
        if ($disabled) echo '<input type="hidden" name="data['.$parameter.']" value="'.(isset($data[$parameter]) ? $data[$parameter] : "").'" />';
        ?>

    </div>
    <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
</div>