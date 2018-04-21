<?php

if (isset($historyBlock['historyItem'] -> type) && isset($FinancialOperationTypeList[$historyBlock['historyItem'] -> type]) && $historyBlock['operationID']) {
    ?>
    <a href="#" title="Переглянути операцію">
        <i class="icons icons_look" onclick="getReceiptOperation('<?=$historyBlock['operationID']?>', <?=$historyBlock['historyItem'] -> type?>, 'cabinet_remittance_<?=$FinancialOperationTypeList[$historyBlock['historyItem'] -> type]?>'); return false;"></i>
    </a>
    <?php
} else {
    ?>
    <a href="#" title="Переглянути операцію">
        <i class="icons icons_look" onclick="getReceiptOperationDef('<?=(htmlspecialchars(serialize($historyBlock['historyItem']),ENT_QUOTES ))?>'); return false;"></i>
    </a>
    <?php
}

require_once $tpl -> pathFull."/snippets/cabinet_finance_operations_view_receipt_scripts.php"

?>

<div id="get_receipt_operation_modal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{CABINET-OPETATION-RECEIPT}</h4>
            </div>
            <div class="modal-body preloader_block ">
                <div class="preloader_content">

                </div>

                <img class="preloader" src="<?=$tpl->path?>/images/preloader.gif" />
            </div>
        </div>
    </div>
</div>






