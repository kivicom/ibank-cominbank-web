<?php

$listCardsFinancialOperation = [];

foreach ($IB -> CONSTANTS['FinancialOperationType'] as $index => $item)
    if (strpos($item, "CARD") !== false) $listCardsFinancialOperation[] = $index;

$display = (isset($historyBlock['historyItem']) && isset($historyBlock['historyItem'] -> subject -> type) && in_array($historyBlock['historyItem'] -> subject -> type, $listCardsFinancialOperation)) ? false: true;

if ($display):
?>
<form class="perform" method="post" action="/<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_REMITTANCE_URL'))?>/?FinancialOperationType=<?=$historyBlock['historyItem'] -> type?>">

    <input type="hidden" name="data[operationId]" value="<?=$historyBlock['operationID']?>"/>
    <input type="hidden" name="SERVICE" value="operation"/>
    <a href="#" role="button" class="icons icons_repeat"
       data-toggle="popover"
       data-content="
        <button class='operation-action' type='submit'>{CABINET-OPERATION-REPEAT-OPERATION}</button><br/>
        <a class='operation-action' href='#' onclick='system_template_modal(jQuery(this)); return false;' data-operation_id='<?=$historyBlock['operationID']?>' data-finance_type='<?=$historyBlock['historyItem'] -> type?>'
    >{CABINET-REMITTANCE-SAVE-TEMPLATE}</a>"
       data-placement="top"
       onclick="return false;"
    >

</form>
<?php
endif;
?>