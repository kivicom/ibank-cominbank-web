<?php
$id = isset($id) ? $id : "input_" . uniqid();
$disabled = isset($disabled) ? $disabled : false;
$parameter = isset($parameter) ? $parameter : 'accountNumber';
$label = isset($label) ? $label : '';

if (isset($data[$parameter]) && ($data[$parameter] === false)) {

} else {
    if(isset($_GET['AJAX'])) {
?>
        <div class="remittance_form_input_default form-group row">
            <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
            <label class="col-md-3 col-sm-3 col-xs-12 text-left"><?= $label ?></label>
            <div class="col-md-7 col-sm-7 col-xs-12">
                <input <?= $id ? ("id='" . $id . "'") : "" ?> class="required form-control"
                                                              name="data[<?= $parameter ?>]"
                                                              type="tel"
                                                              data-mask='digits'
                                                              value="<?= isset($data[$parameter]) ? htmlentities($data[$parameter]) : "" ?>" <?= $disabled ? 'disabled="disabled"' : "" ?>>
                <?php
                if ($disabled) echo '<input type="hidden" name="data[' . $parameter . ']" value="' . (isset($data[$parameter]) ? htmlentities($data[$parameter]) : "") . '" />';
                ?>

            </div>
            <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
        </div>
        <?php
    }else{
        ?>
        <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
        <div class="remittance_form_account_input col-md-4 col-sm-4">
            <div class="panel panel-default panel-card col-md-12 col-sm-12 col-xs-12">
                <div class="panel-body">
                    <div class="form-group ">
                        <label><?= $label ?></label>
                        <input <?= $id ? ("id='" . $id . "'") : "" ?> class="required form-control"
                                                                      name="data[<?= $parameter ?>]"
                                                                      type="tel"
                                                                      data-mask='digits'
                                                                      value="<?= isset($data[$parameter]) ? htmlentities($data[$parameter]) : "" ?>" <?= $disabled ? 'disabled="disabled"' : "" ?>>
                        <?php
                        if ($disabled) echo '<input type="hidden" name="data[' . $parameter . ']" value="' . (isset($data[$parameter]) ? htmlentities($data[$parameter]) : "") . '" />';
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
        <?php
    }
}
?>

<script>

</script>