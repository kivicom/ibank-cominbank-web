<?php
$FinancialOperation = strtr(basename(__FILE__, '.php'), array("cabinet_remittance_" => ""));

$CONTRACT_TO_CONTRACT = false;

if ($SERVICE !== false) {
    $ContractReference = new \ContractReference();
    $ContractReference->id = $data['id'];
    $ContractReference->type = $data['type'];

    $ContractReferenceTo = new \ContractReference();
    $ContractReferenceTo->id = $data['id_to'];
    $ContractReferenceTo->type = $data['type_to'];

    $AllTemplates = $IB->request("findAllOperationTemplates", [
        $IB->TOKEN
    ]);

    $templateId = false;

    //Для перевода между собственными счетами выбираем шаблоны с origin = 0 AND operationSubject -> type = 1 (CONTRACT_TO_CONTRACT)
    if ($AllTemplates)
        foreach ($AllTemplates as $template) {
            if (
                isset($template->origin)
                && ($template->origin == "0")
                && isset($template->operationSubject->type)
                && ($template->operationSubject->type == "1")
                && isset($template->operationSubject->subjectContract->id)
                && isset($template->operationSubject->subjectContract->type)
                && ($template->operationSubject->subjectContract->id == $data['id_to'])
                && ($template->operationSubject->subjectContract->type == $data['type_to'])
            ) {
                $templateId = $template->id;
                break;
            }
        }

    switch ($SERVICE) {
        case "enroll":
            $CONTRACT_TO_CONTRACT = $IB->request("prefaceTemplateOperation", [
                $IB->TOKEN,
                $templateId,
                $amountInCents,
                $ContractReference,
                new \Attributes()
            ]);

            $SERVICE = $CONTRACT_TO_CONTRACT ? (($CONTRACT_TO_CONTRACT->status == 0) ? "commit" : $SERVICE) : "form";
            break;

        case "commit":
            $CONTRACT_TO_CONTRACT = $IB->request("executeTemplateOperation", [
                $IB->TOKEN,
                $templateId,
                $amountInCents,
                $ContractReference,
                new \Attributes()
            ]);

            if ($CONTRACT_TO_CONTRACT){
                switch ($CONTRACT_TO_CONTRACT->status){
                    case 0:
                    case 3:
                        $SERVICE = "success";
                        break;
                    case 1:
                        $SERVICE = "form";
                        break;
                }
            }else{
                $SERVICE = "form";
            }
            break;

        case "new_template":
        case "edit_template":
        case "delete_template":
            $FinancialOperationSubject = new \FinancialOperationSubject();
            $FinancialOperationSubject -> type = $FinancialOperationTypeActive;
            $FinancialOperationSubject -> subjectContract = $ContractReferenceTo;

            include "cabinet_remittance_template_process.php";
            break;

        case "operation":
            $data = array_merge(
                $data,
                (array) $operation -> srcContractRef,
                ['finance_type' => $data['finance_type']]
            );
            $data['id_to'] = $operation -> dstContractRef -> id;
            $data['type_to'] = $operation -> dstContractRef -> type;
            $data['amountInCents'] = $operation -> amountInCents;
            $data['currency'] = $operation -> currency;

            $SERVICE = "form";
            break;

    }

    $EXCEPTION = $IB->EXCEPTION_process();
}

$financialOperationResult = $CONTRACT_TO_CONTRACT;


?>

