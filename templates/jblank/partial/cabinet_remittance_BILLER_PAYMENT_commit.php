<?php
$formId = "form_" . uniqid();
$IB->includes("blocks/cabinet_remittance_nav", [
    'data' => $data,
    'SERVICE' => $SERVICE,
    'title' => ""
]);
?>

<form id="<?= $formId ?>" class="validate" method="post" action="">
    <input type="hidden" name="SERVICE" value="commit"/>

    <?php
    $disabled = true;

    $getParameters = array_merge((array)$BILLER_PAYMENT->attributes->attrs, ["cardId" => $data['card']], ['amountInCents' => $BILLER_PAYMENT->amountInCents]);
    include "cabinet_remittance_BILLER_PAYMENT_fields.php";

    if ($BILLER_PAYMENT->operationConditions->commission)
        $IB->includes("blocks/remittance_form_amount", [
            'data' => [
                'commission' => isset($BILLER_PAYMENT->operationConditions->commission) ? $BILLER_PAYMENT->operationConditions->commission : 0,
                'currency' => $BILLER_PAYMENT->currency
            ],
            'parameter' => 'commission',
            'label' => '{CABINET-REMITTANCE-COMISSION}',
            'disabled' => true,
            'required' => false
        ]);

    if (isset($BILLER_PAYMENT->operationConditions->extAuthRequired))
        if ($BILLER_PAYMENT->operationConditions->extAuthRequired === true) {
            ?>
            <input class="system_ext_auth_required required" type="hidden" value=""/>
            <?php
        }

    $IB->includes("blocks/remittance_form_submit", ['FinancialOperation' => $FinancialOperation]);
    ?>

</form>



