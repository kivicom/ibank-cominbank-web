<div class="form-templates__title">
    {CABINET-REMITTANCE-<?=$FinancialOperation?>-TITLE}
</div>

<?php
$formId = "form_".uniqid();
?>
<form id="<?=$formId?>" class="cabinet_remittance_form_<?=$FinancialOperationTypeActive?> validate" action="?FinancialOperationType=<?=$FinancialOperationTypeActive?>" method="post" class="validate">
    <input type="hidden" name="SERVICE" value="commit" />
    <?php

    $getParameters = array_merge((array)$IntrabankTransferOperation->srcContractRef, (array)$IntrabankTransferOperation->subject->intrabankOperationSubject, ["cardId" => $data['card']], ['amountInCents' => $IntrabankTransferOperation->amountInCents]);

    $disabled = true;


    // упрощенная форма проверки правильности деталей платежа
    $fieldsFile = $tpl -> partialTheme."/cabinet_remittance_checkfield.php";
    $fieldsFileExist = file_exists($fieldsFile);
    if($fieldsFileExist) {
        $allAccounts = $IB->getAllAccounts();
        foreach ($allAccounts as $singleAccount) {
            if ($singleAccount['id'] == $financialOperationResult->srcContractRef->id &&
                ( (isset($singleAccount['cardId']) && isset($data['card'])) ? $singleAccount['cardId'] == $data['card'] : TRUE)) {
                $titleSrc = $singleAccount['title'];
                break;
            }
        }
        $currency = $financialOperationResult->currency ? ' {' . $financialOperationResult->currency . '}' : '';
        $amount = $financialOperationResult->amountInCents;
        $comission = $financialOperationResult->operationConditions->commission;
        $total = $tpl->priceFormat(preg_replace('/\D/', '', $amount + $comission)) . $currency;
        $amount = $tpl->priceFormat(preg_replace('/\D/', '', $amount)) . $currency;
        $comission = $tpl->priceFormat(preg_replace('/\D/', '', $comission)) . $currency;
        $checkoutFields = array(
            'from' => array(
                'label' => '{CABINET-REMITTANCE-' . $FinancialOperation . '-FROM}',
                'value' => $titleSrc
            ),
            'to' => array(
                'label' => '{CABINET-REMITTANCE-' . $FinancialOperation . '-TO}',
                'value' => $financialOperationResult->subject->intrabankOperationSubject->identifier
            ),
            'recipientName' => array(
                'label' => '{CABINET-REMITTANCE-' . $FinancialOperation . '-RECIPIENT}',
                'value' => $financialOperationResult->subject->intrabankOperationSubject->challengeRequestValue
            ),
            'amount' => array(
                'label' => '{CABINET-REMITTANCE-AMOUNT}',
                'value' => $amount
            ),
            'commission' => array(
                'label' => '{CABINET-REMITTANCE-COMISSION}',
                'value' => $comission
            ),
            'total' => array(
                'label' => '{CABINET-REMITTANCE-AMOUNT-TOTAL}',
                'value' => $total
            )
        );
        include $fieldsFile;
        echo '<div style="display:none">';
    }
    include "cabinet_remittance_".$FinancialOperation."_fields.php";

    $IB->includes("blocks/remittance_form_amount", [
        'data' => [
            'commission' => isset($IntrabankTransferOperation->operationConditions->commission) ? $IntrabankTransferOperation->operationConditions->commission : 0,
            'currency' => $IntrabankTransferOperation->currency
        ],
        'parameter' => 'commission',
        'label' => '{CABINET-REMITTANCE-COMISSION}',
        'disabled' => true,
        'required' => false
    ]);

    $IB->includes("blocks/remittance_form_input_default", [
        'data' => [
            'challengeRequestValue' => (isset($IntrabankTransferOperation -> subject -> intrabankOperationSubject -> challengeRequestValue) ? $IntrabankTransferOperation -> subject -> intrabankOperationSubject -> challengeRequestValue : ""),
        ],
        'label' => '{CABINET-REMITTANCE-' . $FinancialOperation . '-RECIPIENT}',
        'parameter' => 'challengeRequestValue',
        'disabled' => true,
        'required' => false
    ]);

    if($fieldsFileExist){ ?>
        <input class="required form-check-input" name="data[confirm_recipient]" type="checkbox" value="" checked="checked">
        </div>
        <?php
    }



    if (isset($IntrabankTransferOperation -> operationConditions -> extAuthRequired))
        if ($IntrabankTransferOperation -> operationConditions -> extAuthRequired === true) {
            ?>
            <input class="system_ext_auth_required required" type="hidden" value=""  />
            <?php
        }

    $IB -> includes("blocks/remittance_form_submit",['FinancialOperation' => $FinancialOperation]);
    ?>
</form>
