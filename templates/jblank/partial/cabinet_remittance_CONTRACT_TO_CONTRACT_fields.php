<?php
$disabled = isset($disabled) ? $disabled : false;

$id_from = isset($id_from) ? $id_from : "select_" . uniqid();
$id_to = isset($id_to) ? $id_to : "select_" . uniqid();

$IB->includes("blocks/remittance_form_accounts_list", [
    'id' => $id_from,
    'data' => $getParameters,
    'label' => '{CABINET-REMITTANCE-' . $FinancialOperation . '-FROM}',
    'paremeters' => ['id', 'type', 'cardId' => 'card', 'currency'],
    'disabled' => (isset($DISABLED) && isset($DISABLED['contract_list'])) ? $DISABLED['contract_list'] : $disabled,
    'arrow' => TRUE
]);

$IB->includes("blocks/remittance_form_accounts_list", [
    'id' => $id_to,
    'data' => $getParameters,
    'label' => '{CABINET-REMITTANCE-' . $FinancialOperation . '-TO}',
    'paremeters' => ['id' => 'id_to', 'type' => 'type_to', 'cardId' => 'card_to', 'currency' => 'currency_to'],
    'disabled' => $disabled
]);

$IB->includes("blocks/remittance_form_amount", [
    'data' => $getParameters,
    'parameter' => 'amountInCents',
    'disabled' => (isset($DISABLED) && isset($DISABLED['amountInCents'])) ? $DISABLED['amountInCents'] : $disabled
]);
?>