<div class="cabinet-remittance__success">
    <p>
        {CABINET-REMITTANCE-PAYMENT-<?=$IB -> CONSTANTS['UserOperationStatus'][$CardToContract->status]?>}
        <br/>
        <br/>
    <div>
        <a href="<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_ENTRY_URL'))?>"><button type="submit" class="btn-press">Ok</button></a>
        <?php
        if (isset($financialOperationResult->type))
            include $tpl->pathFullTemplate . "/snippets/success_operation_print_receipt.php";
        ?>

    </div>
    </p>

</div>