<input type="hidden" name="data[billerId]" value="<?= $getParameters['billerId'] ?>"/>
<input type="hidden" name="data[providerId]" value="<?= $getParameters['providerId'] ?>"/>

<?php

$disabled = isset($disabled) ? $disabled : false;
if (!empty($findBillerById->parametersConf))
    foreach ($findBillerById->parametersConf as $parameter) {
        $IB->includes("blocks/remittance_form_" . $IB->CONSTANTS['ParameterType'][$parameter->type], [
            'data' => $getParameters,
            'label' => $parameter->title,
            'placeholder' => $parameter->hint,
            'validationRule' => $parameter->validationRule,
            'parameter' => $parameter->name,
            'disabled' => (isset($DISABLED) && isset($DISABLED[$parameter->name])) ? $DISABLED[$parameter->name] : $disabled
        ]);
    }

$IB->includes("blocks/remittance_form_amount", [
    'data' => $getParameters,
    'parameter' => 'amountInCents',
    'required' => true,
    'disabled' => (isset($DISABLED) && isset($DISABLED['amountInCents'])) ? $DISABLED['amountInCents'] : $disabled
]);

$IB->includes("blocks/remittance_form_accounts_list", [
    'data' => $getParameters,
    'label' => '{BILLER-FROM-ACCOUNT}',
    'ContractType' => [],
    'disabled' => (isset($DISABLED) && isset($DISABLED['contract_list'])) ? $DISABLED['contract_list'] : $disabled
]);
