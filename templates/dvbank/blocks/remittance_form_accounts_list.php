<?php
$id = isset($id) ? $id : "id_" . uniqid();
$disabled = isset($disabled) ? $disabled : false;
$label = isset($label) ? $label : '';
$ContractType = isset($ContractType) ? $ContractType : [];
$data = isset($data) ? (is_array($data) ? $data : []) : [];
$paremeters = isset($paremeters) ? $paremeters : false;
$idPanel1 = "id_" . uniqid();
$idPanel2 = "id_" . uniqid();
$finance_type = isset($data['finance_type']) ? $data['finance_type'] : 0;
?>

<?php
if(isset($_GET['AJAX'])){ ?>
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
<?php }else{
    if($finance_type == 3) { ?>
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
    <?php } else {

    if($finance_type == 2) { ?>
    <div class="clearfix">
        <div class="col-lg-3 col-md-3 col-sm-3 hidden-xs"></div>
    <?php } ?>

    <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
    <div class="remittance_form_accounts_list  col-lg-4 col-md-4 col-sm-4 col-xs-12 <?= !empty($arrow) ? 'direction-arrow' : '' ?>">
        <div class="form-group">
            <label class="text-left"><?= $label ?></label>
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
        <div class="panel panel-default panel-card col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="panel-body">
                <div class="form-group">
                    <label>
                        <?php echo ($finance_type == 9) ? '{CABINET-CARD-DETAIL-PAYMENTCARD}' : '{CABINET-ACCOUNT-DETAIL-ACCOUNTNUMBER}'; ?>
                    </label>
                    <h4 id="<?= $idPanel1 ?>" class="form-text text-muted"></h4>
                </div>

                <div class="form-group balance-group">
                    <label>{CABINET-ACCOUNT-DETAIL-BALANCE}</label>
                    <h4 id="<?= $idPanel2 ?>" class="form-text text-muted form-text--green"></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>

    <script>
        jQuery("#<?=$id?>").change(function () {
            var elem = jQuery(this);
            var title = elem.find("option:selected").data("title");
            var balance = elem.find("option:selected").data("balance_formated");
            jQuery("#<?=$idPanel1?>").html(title);
            jQuery("#<?=$idPanel2?>").html(balance);
        });
        jQuery("#<?=$id?>").change();
    </script>

    <?php if($finance_type == 2) { ?>
        <div class="col-lg-3 col-md-3 col-sm-3 hidden-xs"></div>
    </div>
    <?php }

    }
}?>