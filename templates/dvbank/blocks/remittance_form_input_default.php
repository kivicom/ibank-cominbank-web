<?php
$id = isset($id) ? $id : "input_".uniqid();
$disabled = isset($disabled) ? $disabled : false;
$parameter = isset($parameter) ? $parameter: 'accountNumber';
$label = isset($label) ? $label: '';
$finance_type = isset($data['finance_type']) ? $data['finance_type'] : 0;
if (isset($data[$parameter]) && ($data[$parameter] === false)) {

} else {
    ?>
    <div class="remittance_form_input_default form-group row">
        <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
        <label class="col-md-3 col-sm-3 col-xs-12 text-left"><?= $label ?></label>
        <div class="col-md-7 col-sm-7 col-xs-12">
            <input <?= $id ? ("id='" . $id . "'") : "" ?> class="required form-control <?php echo $finance_type?'input-tips':'' ?>" name="data[<?= $parameter ?>]"
                                                          type="text"
                                                          <?php
                                                          if(in_array($parameter, array('accountNumber', 'taxId'))){ ?>
                                                              data-mask='digits'
                                                          <?php } ?>
                                                          value="<?= isset($data[$parameter]) ? htmlentities($data[$parameter]) : "" ?>" <?= $disabled ? 'disabled="disabled"' : "" ?>>

            <?php if($finance_type) { ?>
                <i title="<?= $label ?>" class="tips--helper">
                    <img src="../../../templates/dvbank/img/content/help.svg" class="icons icons--tips">
                </i>
            <?php }
            if ($disabled) echo '<input type="hidden" name="data[' . $parameter . ']" value="' . (isset($data[$parameter]) ? htmlentities($data[$parameter]) : "") . '" />';
            ?>

        </div>
        <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
    </div>
    <?php
}
?>