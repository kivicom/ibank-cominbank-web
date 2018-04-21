<?php
$DefinedAmount = new \DefinedAmount();
$DefinedAmount -> type = 0;
$DefinedAmount -> amount = $amountInCents;

$OperationTemplateProperties = new \OperationTemplateProperties();
$OperationTemplateProperties -> title = $data['title'];
$OperationTemplateProperties -> description = htmlspecialchars($data['description']);
$OperationTemplateProperties -> currency = $data['currency'];
$OperationTemplateProperties -> definedAmounts = array($DefinedAmount);
$OperationTemplateProperties -> iconRef = $data['iconRef'];
$OperationTemplateProperties -> color = $data['color'];
$OperationTemplateProperties -> attributes = new \Attributes();
$OperationTemplateProperties -> defaultSourceContract = $ContractReference;


switch ($SERVICE) {
    case "new_template":
        if(!empty($_POST['operationId'])){
            $result = $IB -> request(
                "createTemplateFromOperation",
                array(
                    $IB->TOKEN,
                    $OperationTemplateProperties,
                    $_POST['operationId']
                )
            );
        }else{
            $result = $IB -> request(
                "createTemplate",
                array(
                    $IB->TOKEN,
                    $OperationTemplateProperties,
                    $FinancialOperationSubject
                )
            );
        }
        break;

    case "edit_template":
        $result = $IB -> request(
            "updateTemplate",
            array(
                $IB->TOKEN,
                $data['templateId'],
                $OperationTemplateProperties
            ));
        break;

    case "delete_template":
        $resultTemplate = $IB -> request('deleteTemplate', [
            $IB->TOKEN,
            $data['templateId'],
        ]);
        break;
}

ob_clean();
ob_start();
if ($IB->EXCEPTION) {
    $arr = array("request" => "exception");
    echo json_encode($arr);
    $html = ob_get_contents();
    ob_end_clean();
} else {

    $arr = array("request" => "success", "SERVICE" => $SERVICE);
    echo json_encode($arr);
    $html = ob_get_contents();
    ob_end_clean();
}
echo $html;
exit;

