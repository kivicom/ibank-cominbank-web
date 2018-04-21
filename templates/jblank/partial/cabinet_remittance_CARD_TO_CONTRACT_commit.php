<div class="form-templates__title">
    {CABINET-REMITTANCE-<?=$FinancialOperation?>-TITLE}
</div>
<form class="cabinet_remittance_form_<?=$FinancialOperationTypeActive?> validate" action="?FinancialOperationType=<?=$FinancialOperationTypeActive?><?=isset($_GET['secure3DRequest']) ? "&secure3DRequest" : "" ?>" method="post">
    <input type="hidden" name="SERVICE" value="commit"/>

    <?php
    $data['enrolledOperationId'] = $CardToContract->id;

    foreach ($data as $field => $parameter) {
        echo '<input type="hidden" name="data[' . $field . ']" value="' . htmlspecialchars($parameter) . '" />';
    }

    // упрощенная форма проверки правильности деталей платежа
    $fieldsFile = $tpl -> partialTheme."/cabinet_remittance_checkfield.php";
    $fieldsFileExist = file_exists($fieldsFile);
    if($fieldsFileExist){
        $allAccounts = $IB->getAllAccounts();
        foreach ($allAccounts as $singleAccount) {
            if ($singleAccount['id'] == $financialOperationResult->dstContractRef->id &&
                ( (isset($singleAccount['cardId']) && isset($data['card_to'])) ? $singleAccount['cardId'] == $data['card_to'] : TRUE)) {
                $titleDst = $singleAccount['title'];
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
                'value' => $financialOperationResult->subject->subjectCardToContract->senderCard->cardNumberMask
            ),
            'to' => array(
                'label' => '{CABINET-REMITTANCE-' . $FinancialOperation . '-TO}',
                'value' => $titleDst
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

    $IB->includes("blocks/remittance_form_CARDNUM", [
        'FinancialOperation' => $FinancialOperation,
        'data' => $data,
        'label' => '{CABINET-REMITTANCE-' . $FinancialOperation . '-FROM}',
        'parameter' => 'cardNumber',
        'disabled' => true
    ]);

    $IB->includes("blocks/remittance_form_accounts_list", [
        'id' => 'CABINET-REMITTANCE-' . $FinancialOperation . '-TO',
        'data' => array_merge((array)$CardToContract->dstContractRef, ["cardId" => $data['card']]),
        'label' => '{CABINET-REMITTANCE-' . $FinancialOperation . '-TO}',
        'ContractType' => [],
        'disabled' => true
    ]);

    $IB->includes("blocks/remittance_form_amount", [
        'data' => [
            'amountInCents' => $CardToContract->amountInCents,
            'currency' => $CardToContract->currency
        ],
        'parameter' => 'amountInCents',
        'disabled' => true
    ]);

    $IB->includes("blocks/remittance_form_amount", [
        'data' => [
            'commission' => isset($CardToContract->operationConditions->commission) ? $CardToContract->operationConditions->commission : 0,
            'currency' => $CardToContract->currency
        ],
        'parameter' => 'commission',
        'label' => '{CABINET-REMITTANCE-COMISSION}',
        'disabled' => true,
        'required' => false
    ]);

    if($fieldsFileExist) {
        echo '</div>';
    }



    //Secure3D
    if (is_object($CardToContract->subject->subjectCardToContract->secure3DRequest) && $CardToContract->subject->subjectCardToContract->secure3DRequest->enrolled && ($data['PaRes'] == '')) {
        $IB->SESS['data'] = $data;
        $IB->SESSION();
        ?>
    </form>
    <form method="post" action="<?= $CardToContract->subject->subjectCardToContract->secure3DRequest->acsUrl ?>">
        <input type="hidden" name="TermUrl" value="<?= $tpl->url ?>&secure3DRequest"/>
        <input type="hidden" name="PaReq"
               value="<?= $CardToContract->subject->subjectCardToContract->secure3DRequest->paReq ?>"/>
        <input type="hidden" name="MD" value="<?= $CardToContract->subject->subjectCardToContract->secure3DRequest->md ?>"/>
        <?php
        $IB -> includes("blocks/remittance_form_submit",['FinancialOperation' => $FinancialOperation]);
        ?>
    </form>
    <?php

    //OTP
    } elseif ($CardToContract->subject->subjectCardToContract->otpRequired) {
        ?>
        <input class="system_ext_auth_required required no_auth_request" data-type="<?= $FinancialOperation ?>" type="hidden"
               name="data[otp]" data-confirm="false"
               value=""/>
            <?php
            $IB -> includes("blocks/remittance_form_submit",['FinancialOperation' => $FinancialOperation]);
            ?>
        </form>
        <?php
    } else {
        $IB -> includes("blocks/remittance_form_submit",['FinancialOperation' => $FinancialOperation]);
    }

    if (($data['PaRes'] !== '') && ($CardToContract->status == 2)) {
    ?>
    <script>
        jQuery(document).ready(function () {
            jQuery(".cabinet_remittance_form_<?=$FinancialOperationTypeActive?>").submit();
        });
    </script>

    <?php
    }
?>