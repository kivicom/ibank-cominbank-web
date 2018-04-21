<?php
$disabled = isset($disabled) ? $disabled : false;
$parameter = isset($parameter) ? $parameter: 'phone';
$label = isset($label) ? $label: '';
$validationRule = isset($validationRule) ? $validationRule: '';

$validationRule = false;

$placeholder = isset($placeholder) ? $placeholder: '+38 (___) ___ __ __';
$id = isset($id) ? $id : "input_".uniqid();


if (isset($data[$parameter]) && ($data[$parameter] === false)) {

} else {
    ?>
    <div class="remittance_form_PHONE form-group row">
        <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
        <label class="col-md-3 col-sm-3 col-xs-12 text-left"><?= $label ?></label>
        <div class="col-md-7 col-sm-7 col-xs-12">
            <?php
            if ($validationRule) {
                ?>
                <script>
                    var validationRule_<?=$id?> = <?=$validationRule?>;
                </script>
                <?php
            }
            ?>

            <input id="<?= $id ?>" class="required form-control" type="text" data-mask="phone"
                   placeholder="<?= $placeholder ?>"
                   value="<?= (isset($data[$parameter]) ? ("+".preg_replace('/\D/', '', $data[$parameter])) : "") ?>" <?= $disabled ? 'disabled="disabled"' : "" ?>
                   onkeyup="jQuery('[data-for=\'<?=$id?>\']').val(jQuery(this).val().replace(/[^0-9+]/g, ''))"
            >
            <input data-for="<?= $id ?>" type="hidden" name="data[<?= $parameter ?>]"
                   value="<?= (isset($data[$parameter]) ? ("+".preg_replace('/\D/', '', $data[$parameter])) : "") ?>"/>
        </div>
        <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
    </div>
    <?php
}
?>