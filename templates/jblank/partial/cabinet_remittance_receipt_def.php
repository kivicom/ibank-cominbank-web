<?php
$operation = unserialize(html_entity_decode($_POST['data']));

if ($operation) {
?>
    <div class="cabinet_operation_receipt">
        <div class="cabinet_operation_receipt__item">
            <span class="cabinet_operation_receipt__field">{RECEIPT_STATUS_AMOUNT}</span>
            <span class="cabinet_operation_receipt__value"><?=$tpl-> priceFormat($operation -> amountInCents, false)?> <?=$operation -> currency?></span>
        </div>

        <div class="cabinet_operation_receipt__item">
            <span class="cabinet_operation_receipt__field">{RECEIPT_STATUS_DATE}</span>
            <span class="cabinet_operation_receipt__value">
            <?php
            $historyDateTime = isset($operation -> operationDate) ? $operation -> operationDate/1000 : "";
            $historyDate = date("d.m.Y", $historyDateTime);
            $historyTime = date("H:i", $historyDateTime);
            ?>
                <span class="date"><span><?=$historyDate?></span>&nbsp;<span><?=$historyTime?></span></span>
        </span>
        </div>


        <div class="cabinet_operation_receipt__item">
            <span class="cabinet_operation_receipt__field">{RECEIPT_STATUS_FROM}</span>
            <div class="cabinet_operation_receipt__value">
                <div class="cabinet_operation_receipt__item">
                    <span class="cabinet_operation_receipt__field">{RECEIPT_STATUS_ACCOUNT}</span>
                    <span class="cabinet_operation_receipt__value"><?=$operation->source->accountNumber?></span>
                </div>
                <div class="cabinet_operation_receipt__item">
                    <span class="cabinet_operation_receipt__field">{RECEIPT_STATUS_MFO}</span>
                    <span class="cabinet_operation_receipt__value"><?=$operation->source->bankId?></span>
                </div>
            </div>
        </div>


        <div class="cabinet_operation_receipt__item">
            <span class="cabinet_operation_receipt__field">{RECEIPT_STATUS_TO}</span>
            <div class="cabinet_operation_receipt__value">
                <div class="cabinet_operation_receipt__item">
                    <span class="cabinet_operation_receipt__field">{RECEIPT_STATUS_ACCOUNT}</span>
                    <span class="cabinet_operation_receipt__value"><?=$operation->destination->accountNumber?></span>
                </div>
                <div class="cabinet_operation_receipt__item">
                    <span class="cabinet_operation_receipt__field">{RECEIPT_STATUS_MFO}</span>
                    <span class="cabinet_operation_receipt__value"><?=$operation->destination->bankId?></span>
                </div>
            </div>
        </div>

        <div class="cabinet_operation_receipt__item">
            <span class="cabinet_operation_receipt__field">{RECEIPT_STATUS_DESCRIPTION}</span>
            <span class="cabinet_operation_receipt__value"><?=$operation -> description?></span>
        </div>
    </div>

    <?php
}
?>