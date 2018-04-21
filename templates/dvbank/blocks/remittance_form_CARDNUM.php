<?php
$id = isset($id) ? $id : "input_" . uniqid();
$disabled = isset($disabled) ? $disabled : false;
$parameter = isset($parameter) ? $parameter : 'cardNumber';
$label = isset($label) ? $label : '';
?>
<div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
<div class="remittance_form_CARDNUM col-md-4 col-sm-4 col-xs-12 <?= !empty($arrow) ? 'direction-arrow' : '' ?>">
    <div class="panel panel-default panel-card col-md-12">
        <div class="panel-body">
            <div class="form-group">
                <label><?= $label ?></label>
                    <input id="<?=$id?>" class="required form-control" name="data[<?= $parameter ?>]" type="text"
                           placeholder="0000 0000 0000 0000"
                           value="<?= isset($data[$parameter]) ? $data[$parameter] : "" ?>" <?= $disabled ? 'disabled="disabled"' : "data-mask='card_number'" ?>
                           autocomplete="off"
                           readonly
                           onfocus="this.removeAttribute('readonly')"
                    >
                    <?php
                    if ($disabled) echo '<input type="hidden" name="data[' . $parameter . ']" value="' . (isset($data[$parameter]) ? $data[$parameter] : "") . '" />';
                    ?>
            </div>
        </div>
    </div>
</div>
<div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>

<script>
    jQuery(document).ready(function () {
         jQuery("#<?=$id?>").closest(".remittance_form_CARDNUM").find(".panel-body").append(jQuery("#<?=$id?>").closest("form").find(".remittance_form_card_date"));
         jQuery("#<?=$id?>").closest(".cabinet_remittance_form_10 .remittance_form_CARDNUM").find(".panel-body").append(jQuery("#<?=$id?>").closest("form").find(".remittance_form_card_secure_code"));
        jQuery(".cabinet_remittance_form_9 .balance-group").after(jQuery("#<?=$id?>").closest("form").find(".remittance_form_card_secure_code "));
    });
</script>