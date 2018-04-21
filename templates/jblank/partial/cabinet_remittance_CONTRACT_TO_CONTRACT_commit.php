<div class="form-templates__title">
    {CABINET-REMITTANCE-REQUISITES-TITLE}
</div>

<?php
$formId = "form_".uniqid();
?>
<form id="<?=$formId?>" class="validate" action="?FinancialOperationType=<?=$FinancialOperationTypeActive?>" method="post" class="validate">
    <input type="hidden" name="SERVICE" value="commit" />

    <?php
    $getParameters = array_merge(
        (array)$CONTRACT_TO_CONTRACT->srcContractRef,
        ["cardId" => $data['card']],
        ["card_to" => $data['card_to']],
        ["id_to"=> $CONTRACT_TO_CONTRACT->dstContractRef->id],
        ["type_to" => $CONTRACT_TO_CONTRACT->dstContractRef->type],
        ['amountInCents' => $CONTRACT_TO_CONTRACT->amountInCents]
    );
    $disabled = true;

    // упрощенная форма проверки правильности деталей платежа
    $fieldsFile = $tpl -> partialTheme."/cabinet_remittance_checkfield.php";
    $fieldsFileExist = file_exists($fieldsFile);
    if($fieldsFileExist){
        $allAccounts = $IB->getAllAccounts();
        foreach ($allAccounts as $singleAccount){
            if(
                    $singleAccount['id'] == $financialOperationResult->srcContractRef->id &&
                    ( (isset($singleAccount['cardId']) && isset($data['card'])) ? $singleAccount['cardId'] == $data['card'] : TRUE)
            ){
                $titleSrc = $singleAccount['title'];
            }
            if(
                    $singleAccount['id'] == $financialOperationResult->dstContractRef->id &&
                    ( (isset($singleAccount['cardId']) && isset($data['card_to'])) ? $singleAccount['cardId'] == $data['card_to'] : TRUE)
            ){
                        $titleDst = $singleAccount['title'];
            }
            if(!empty($titleSrc) && !empty($titleDst)) break;
        }
        $currency = $financialOperationResult->currency ? ' {'.$financialOperationResult->currency.'}' : '';
        $amount = $financialOperationResult->amountInCents;
        $comission = $financialOperationResult->operationConditions->commission;
        $total = $tpl->priceFormat(preg_replace('/\D/', '', $amount + $comission)).$currency;
        $amount = $tpl->priceFormat(preg_replace('/\D/', '', $amount)).$currency;
        $comission = $tpl->priceFormat(preg_replace('/\D/', '', $comission)).$currency;
        $checkoutFields = array(
            'from'  =>  array(
                            'label' => '{CABINET-REMITTANCE-' . $FinancialOperation . '-FROM}',
                            'value' => $titleSrc
                        ),
            'to'    =>  array(
                            'label' =>  '{CABINET-REMITTANCE-' . $FinancialOperation . '-TO}',
                            'value' => $titleDst
                    ),
            'amount'    =>  array(
                            'label' =>  '{CABINET-REMITTANCE-AMOUNT}',
                            'value' =>  $amount
                    ),
            'commission'    =>  array(
                        'label' =>  '{CABINET-REMITTANCE-COMISSION}',
                        'value' =>  $comission
                    ),
            'total' =>  array(
                        'label' =>  '{CABINET-REMITTANCE-AMOUNT-TOTAL}',
                        'value' =>  $total
                    )
        );
        include $fieldsFile;
        echo '<div style="display:none">';
    }
    include "cabinet_remittance_".$FinancialOperation."_fields.php";
    if($fieldsFileExist){
        echo '</div>';
    }

    $IB -> includes("blocks/remittance_form_submit",['FinancialOperation' => $FinancialOperation]);

    /*if (isset($SEP_TRANSFER -> operationConditions -> extAuthRequired))
        if ($SEP_TRANSFER -> operationConditions -> extAuthRequired === true) {
            $getAuthSession = $IB -> request("getAuthSession", [
                $IB->TOKEN
            ]);
            echo ($getAuthSession->level)? '' : '<input class="system_ext_auth_required required" type="hidden" value="" />';
        }*/
    ?>
</form>
