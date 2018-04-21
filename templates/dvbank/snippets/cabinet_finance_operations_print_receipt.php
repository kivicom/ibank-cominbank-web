<form method="post" action="/uk/receipts/receipt" target="_blank">
    <input type="hidden" name="FinancialOperationType" value="<?=$historyBlock['historyItem'] -> type?>" />
    <input type="hidden" name="TEMPLATE" value="cabinet_remittance_<?=$FinancialOperationTypeList[$historyBlock['historyItem'] -> type]?>" />
    <input type="hidden" name="operationId" value="<?=$historyBlock['operationID']?>" />
    <input type="hidden" name="receipt_type" value="pdf" />
    <button class="popovers icons icons_load" data-type='PDF' title="{CABINET-OPERATION-SEND-RECEIPT}"></button>
</form>

<script>
    jQuery("body").on("click", ".receipt_generate", function () {
        var type=jQuery(this).data("type");
        jQuery(this).closest("form").find("input[name='receipt_type']").val(type);
    })
</script>