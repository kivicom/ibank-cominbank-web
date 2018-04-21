<?php
$disabled = isset($disabled) ? $disabled : false;

$IB->includes("blocks/remittance_form_accounts_list", [
    'data' => $getParameters,
    'label' => '{CABINET-REMITTANCE-' . $FinancialOperation . '-FROM}',
    'ContractType' => [],
    'disabled' => (isset($DISABLED) && isset($DISABLED['contract_list'])) ? $DISABLED['contract_list'] : $disabled
]);

$IB -> includes("blocks/remittance_form_input_default", [
    'data' => $getParameters,
    'label' => '{CABINET-REMITTANCE-'.$FinancialOperation.'-TO}',
    'parameter' => 'accountNumber',
    'disabled' => (isset($DISABLED) && isset($DISABLED['accountNumber'])) ? $DISABLED['accountNumber'] : $disabled
]);

$IB -> includes("blocks/remittance_form_MFO", [
    'data' => $getParameters,
    'label' => '{CABINET-REMITTANCE-'.$FinancialOperation.'-MFO}',
    'parameter' => 'bankId',
    'input_bankname' => $FinancialOperation."_reciver",
    'disabled' => (isset($DISABLED) && isset($DISABLED['bankId'])) ? $DISABLED['bankId'] : $disabled
]);

$IB -> includes("blocks/remittance_form_input_default", [
    'data' => $getParameters,
    'label' => '{CABINET-REMITTANCE-'.$FinancialOperation.'-RECIVER}',
    'parameter' => 'name',
    'disabled' => (isset($DISABLED) && isset($DISABLED['name'])) ? $DISABLED['name'] : $disabled
]);

$IB -> includes("blocks/remittance_form_input_default", [
    'data' => $getParameters,
    'label' => '{CABINET-REMITTANCE-'.$FinancialOperation.'-IPN}',
    'parameter' => 'taxId',
    'disabled' => (isset($DISABLED) && isset($DISABLED['taxId'])) ? $DISABLED['taxId'] : $disabled
]);

$IB -> includes("blocks/remittance_form_textarea_default", [
    'data' => $getParameters,
    'label' => '{CABINET-REMITTANCE-'.$FinancialOperation.'-PURPOSE}',
    'parameter' => 'destinationDescription',
    'maxlength' => 150,
    'required' => false,
    'disabled' => (isset($DISABLED) && isset($DISABLED['destinationDescription'])) ? $DISABLED['destinationDescription'] : $disabled
]);

$IB -> includes("blocks/remittance_form_amount", [
    'data' => $getParameters,
    'parameter' => 'amountInCents',
    'required' => true,
    'disabled' => (isset($DISABLED) && isset($DISABLED['amountInCents'])) ? $DISABLED['amountInCents'] : $disabled
]);

?>