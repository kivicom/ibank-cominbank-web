<?php
$FinancialOperationTypeList = $IB -> CONSTANTS['FinancialOperationType'];
?>
<form method="post" action="/uk/receipts/receipt" target="_blank">
    <input type="hidden" name="FinancialOperationType" value="<?=$financialOperationResult -> type?>" />
    <input type="hidden" name="TEMPLATE" value="cabinet_remittance_<?=$FinancialOperationTypeList[$financialOperationResult -> type]?>" />
    <input type="hidden" name="operationId" value="<?=$financialOperationResult->id?>" />
    <input type="hidden" name="receipt_type" value="pdf" />
    <a href="#" role="button" class="btn-press"
       data-toggle="popover"
       title="{CABINET-OPERATION-SEND-RECEIPT}"
       data-content="<?php
       foreach ($IB->CONSTANTS['ReportFormatType'] as $item) {
           ?>
                    <button class='receipt_generate' data-type='<?=$item?>' type='submit'><?=$item?></button>&nbsp;
                                                <?php
       }
       ?>"
       data-original-title="test title"
       data-placement="top"
       onclick="return false;"
    >{CABINET-OPERATION-SEND-RECEIPT}
    </a>
</form>
<script>
    jQuery("body").on("click", ".receipt_generate", function () {
        var type=jQuery(this).data("type");
        jQuery(this).closest("form").find("input[name='receipt_type']").val(type);
    })
</script>