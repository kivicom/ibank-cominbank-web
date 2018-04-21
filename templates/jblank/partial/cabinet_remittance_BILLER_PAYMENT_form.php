<?php
$formId = "form_" . uniqid();
$IB->includes("blocks/cabinet_remittance_nav", [
    'data' => $data,
    'SERVICE' => $SERVICE,
    'title' => ""
]);
?>

<form id="<?= $formId ?>" class="validate" method="post" action="">
    <input type="hidden" name="SERVICE" value="enroll"/>

    <?php
    $getParameters = $data;

    include "cabinet_remittance_BILLER_PAYMENT_fields.php";

    if (false) {
        if (isset($prefaceBillPaymentOperation->operationConditions->extAuthRequired))
            if ($prefaceBillPaymentOperation->operationConditions->extAuthRequired === true) {
                ?>
                <input class="system_ext_auth_required required" type="hidden" value=""/>
                <?php
            }

        if (isset($prefaceBillPaymentOperation->operationConditions->commission) && $prefaceBillPaymentOperation->operationConditions->commission) {
            $IB->includes("blocks/remittance_form_amount", [
                'data' => [
                    'commission' => isset($prefaceBillPaymentOperation->operationConditions->commission) ? $prefaceBillPaymentOperation->operationConditions->commission : 0,
                    'currency' => $IntrabankTransferOperation->currency
                ],
                'parameter' => 'commission',
                'label' => '{CABINET-REMITTANCE-COMISSION}',
                'disabled' => true,
                'required' => false
            ]);
        }
    }

    $IB->includes("blocks/remittance_form_submit", ['FinancialOperation' => $FinancialOperation]);
    ?>

</form>


