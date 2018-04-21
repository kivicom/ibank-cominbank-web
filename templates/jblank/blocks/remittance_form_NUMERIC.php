<?php
$id = isset($id) ? $id : "input_".uniqid();
$disabled = isset($disabled) ? $disabled : false;
$parameter = isset($parameter) ? $parameter: 'accountNumber';
$label = isset($label) ? $label: '';
$placeholder = isset($placeholder) ? $placeholder: '';

if (isset($data[$parameter]) && ($data[$parameter] === false)) {

} else {
    ?>
    <div class="remittance_form_NUMERIC form-group row">
        <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
        <label class="col-md-3 col-sm-3 col-xs-12 text-left"><?= $label ?></label>
        <div class="col-md-7 col-sm-7 col-xs-12">
            <input <?= $id ? ("id='" . $id . "'") : "" ?> class="required form-control" name="data[<?= $parameter ?>]"
                                                          type="text"
                                                          value="<?= isset($data[$parameter]) ? htmlentities($data[$parameter]) : "" ?>" <?= $disabled ? 'disabled="disabled"' : "" ?>
                                                          placeholder="<?=$placeholder?>"
                                                          data-mask='digits'
            >
            <?php
            if ($disabled) echo '<input type="hidden" name="data[' . $parameter . ']" value="' . (isset($data[$parameter]) ? htmlentities($data[$parameter]) : "") . '" />';
            ?>

        </div>
        <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
    </div>
    <?php
}
?>