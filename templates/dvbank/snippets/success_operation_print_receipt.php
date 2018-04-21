<?php
$FinancialOperationTypeList = $IB -> CONSTANTS['FinancialOperationType'];
?>
<form method="post" action="/uk/receipts/receipt" target="_blank">
    <input type="hidden" name="FinancialOperationType" value="<?=$financialOperationResult -> type?>" />
    <input type="hidden" name="TEMPLATE" value="cabinet_remittance_<?=$FinancialOperationTypeList[$financialOperationResult -> type]?>" />
    <input type="hidden" name="operationId" value="<?=$financialOperationResult->id?>" />
    <input type="hidden" name="receipt_type" value="pdf" />
    <button class="btn-press" data-type='PDF' title="{CABINET-OPERATION-SEND-RECEIPT}">{CABINET-OPERATION-SEND-RECEIPT}
    </button>
</form>
<script>
    jQuery("body").on("click", ".receipt_generate", function () {
        var type=jQuery(this).data("type");
        jQuery(this).closest("form").find("input[name='receipt_type']").val(type);
    })
</script>