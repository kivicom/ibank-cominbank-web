<?php
$historyDateTime = isset($operation -> executeDate) ? $operation -> executeDate/1000 : "";
$historyDate = date("d.m.Y", $historyDateTime);
$historyTime = date("H:i", $historyDateTime);

$approveDateTime = isset($operation -> approveDate) ? $operation -> approveDate/1000 : "";
$approveDate = date("d.m.Y", $approveDateTime);
$approveTime = date("H:i", $approveDateTime);

$cardNumberMask = $operation -> subject -> subjectCardToCard -> senderCard -> cardNumberMask;
$cardNumberMaskTo = $operation -> subject -> subjectCardToCard -> receiverCard -> cardNumberMask;

if (isset($_POST['receipt_type'])) { //Условие не срабатывает (вся обработка выполняется в receipt.php)

    $mpdf = new mPDF('utf-8', 'A4', '8', '', 10, 10, 7, 7, 10, 10); /*задаем формат, отступы и.т.д.*/

    $mpdf->charset_in = 'utf-8'; /*не забываем про русский*/

    ob_start();
    ?>
    <style>
        div, table, td, span {
            font-family: Arial;
            font-size: 10pt;
        }

        td {
            padding: 10px 10px;
        }

        td, div {
            border-color: #ccc;
            border-style: solid;
        }

        table {
            border-collapse: collapse;
        }
    </style>

    <?php
    $replace = array(
        "%amount%" => $tpl-> priceFormat($operation -> amountInCents, false)." ".$operation -> currency,
        "%amount_str%" => $tpl -> num2str(round(($operation -> amountInCents / 100), 2), $tpl -> lang),
        '%date%' => $tpl -> dateStr($historyDateTime, $tpl -> lang),
        '%approveDate%' => $tpl -> dateStr($approveDateTime, $tpl -> lang),
        '%srcDebet%' => $cardNumberMask,
        '%srcCredit%' => "",
        '%details%' => $operation -> description,
        '%dstBankCode%' => ''
    );
    $content = strtr($content, $replace);

    ?>

    <?=$content?>
    <?php

    $html = ob_get_contents();
    ob_end_clean();

    header("Content-type:application/pdf");
    $mpdf->WriteHTML($html); /*формируем pdf*/
    echo $mpdf->Output('receipt.pdf', 'S');
    exit();
} else {
    ?>
    <div class="cabinet_operation_receipt">
        <div class="cabinet_operation_receipt__item">
            <span class="cabinet_operation_receipt__field">{RECEIPT_STATUS_AMOUNT}</span>
            <span class="cabinet_operation_receipt__value"><?=$tpl-> priceFormat($operation -> amountInCents, false)?> <?=$operation -> currency?></span>
        </div>

        <div class="cabinet_operation_receipt__item">
            <span class="cabinet_operation_receipt__field">{RECEIPT_STATUS_COMMISSION}</span>
            <span class="cabinet_operation_receipt__value"><?=$tpl-> priceFormat($operation -> operationConditions -> commission, false)?> <?=$operation -> currency?></span>
        </div>

        <div class="cabinet_operation_receipt__item">
            <span class="cabinet_operation_receipt__field">{RECEIPT_STATUS_DATE}</span>
            <span class="cabinet_operation_receipt__value">
                <?php
                $historyDateTime = isset($operation -> executeDate) ? $operation -> executeDate/1000 : "";
                $historyDate = date("d.m.Y", $historyDateTime);
                $historyTime = date("H:i", $historyDateTime);
                ?>
                <span class="date"><span><?=$historyDate?></span>&nbsp;<span><?=$historyTime?></span></span>
            </span>
        </div>

        <div class="cabinet_operation_receipt__item">
            <span class="cabinet_operation_receipt__field">{RECEIPT_STATUS_STATUS}</span>
            <span class="cabinet_operation_receipt__value">
                {RECEIPT_STATUS_<?=$IB -> CONSTANTS['UserOperationStatus'][$operation -> status]?>}
            </span>
        </div>

        <div class="cabinet_operation_receipt__item">
            <span class="cabinet_operation_receipt__field">{RECEIPT_STATUS_FROM}</span>
            <div class="cabinet_operation_receipt__value">
                <div class="cabinet_operation_receipt__item">
                    <span class="cabinet_operation_receipt__field">{CARD}</span>
                    <span class="cabinet_operation_receipt__value"><?=$cardNumberMask?></span>
                </div>
            </div>
        </div>


        <div class="cabinet_operation_receipt__item">
            <span class="cabinet_operation_receipt__field">{RECEIPT_STATUS_TO}</span>
            <div class="cabinet_operation_receipt__value">
                <div class="cabinet_operation_receipt__item">
                    <span class="cabinet_operation_receipt__field">{CARD}</span>
                    <span class="cabinet_operation_receipt__value"><?=$cardNumberMaskTo?></span>
                </div>
            </div>
        </div>
    </div>
    <?php
}

?>
