<?php
$FinancialOperationTypeActive = isset($_GET['FinancialOperationType']) ? $_GET['FinancialOperationType'] : 1;
$FinancialOperationTypeList = $IB->CONSTANTS['FinancialOperationType'];
$FinancialOperation = $IB->CONSTANTS['FinancialOperationType'][$FinancialOperationTypeActive];
$data = isset($_POST['data']) ? $_POST['data'] : (isset($_GET['data']) ? $_GET['data'] : false);

$parameters = [
    'accountNumber' => '',
    'acsUrl' => '',
    'amountInCents' => '',
    'bankId' => '',
    'billerId' => '',
    'card' => '',
    'cardNumberTo' => '',
    'challengeResponseValue' => '',
    'currency' => '',
    'description' => '',
    'destinationDescription' => '',
    'detection_type' => '',
    'DIRECT' => '',
    'enrolledOperationId' => '',
    'expMonth' => date('m'),
    'expYear' => date('Y'),
    'finance_type' => $FinancialOperationTypeActive,
    'id' => '',
    'id_to' => '',
    'identifier' => '',
    'MD' => '',
    'name' => '',
    'operationId' => '',
    'otp' => '',
    'PaReq' => '',
    'PaRes' => '',
    'phone_number' => '',
    'providerId' => '',
    'secureCode' => '',
    'taxId' => '',
    'templateId' => '',
    'TermUrl' => '',
    'title' => '',
    'type' => '',
    'type_to' => '',
    'wallet_number' => ''
];

if (is_array($data) && count($data))
    foreach ($data as $field => $datum) {
        if (!is_array($datum))
            $parameters[$field] = html_entity_decode($datum);
    }

$data = $parameters;

$antiXss = new AntiXSS();
$antiXssParams = ['description', 'destinationDescription', 'name', 'title'];
foreach ($antiXssParams as $param){
    $data[$param] = $antiXss->xss_clean($data[$param]);
}

?>