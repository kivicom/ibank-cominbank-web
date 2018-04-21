<?php
$id = isset($id) ? $id: false;
$disabled = isset($disabled) ? $disabled : false;
$label = isset($label) ? $label: '';
$ContractType = isset($ContractType) ? $ContractType: [];
$data = isset($data) ? (is_array($data) ? $data : []) : [];
$paremeters = isset($paremeters) ? $paremeters: false;
?>

<div class="form-group row">
    <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
    <label class="col-md-3 col-sm-3 col-xs-12 text-left"><?=$label?></label>
    <div class="col-md-7 col-sm-7 col-xs-12">
        <?php
        $IB->includes("snippets/remittance_form_accounts_list", [
            'id' => $id,
            'ContractType' => $ContractType,
            'selected' => $data,
            'paremeters' => $paremeters,
            'disabled' => $disabled
        ]);
        ?>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
</div>