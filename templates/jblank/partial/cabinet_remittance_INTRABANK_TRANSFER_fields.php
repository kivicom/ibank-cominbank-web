<input type="hidden" name="data[challengeResponseValue]" value="<?=$getParameters['challengeResponseValue']?>" />

<?php
$disabled = isset($disabled) ? $disabled : false;

$IB->includes("blocks/remittance_form_accounts_list", [
    'id' => 'CABINET-REMITTANCE-' . $FinancialOperation . '-TO',
    'data' => $getParameters,
    'label' => '{CABINET-REMITTANCE-' . $FinancialOperation . '-FROM}',
    'ContractType' => [],
    'disabled' => (isset($DISABLED) && isset($DISABLED['contract_list'])) ? $DISABLED['contract_list'] : $disabled,
    'arrow' => TRUE
]);

$IB->includes("blocks/remittance_form_account_input.php", [
    'data' => $getParameters,
    'label' => '{CABINET-REMITTANCE-' . $FinancialOperation . '-TO}',
    'parameter' => 'identifier',
    'disabled' => (isset($DISABLED) && isset($DISABLED['identifier'])) ? $DISABLED['identifier'] : $disabled
]);

$IB->includes("blocks/remittance_form_amount", [
    'data' => $getParameters,
    'parameter' => 'amountInCents',
    'disabled' => (isset($DISABLED) && isset($DISABLED['amountInCents'])) ? $DISABLED['amountInCents'] : $disabled
]);

?>