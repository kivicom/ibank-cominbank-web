<?php
$FinancialOperation = strtr(basename(__FILE__, '.php'), array("cabinet_remittance_" => ""));

$SEP_TRANSFER = false;

if ($SERVICE !== false) {
    $OperationSubject = new \OperationSubject();
    $OperationSubject->accountNumber = $data['accountNumber'];
    $OperationSubject->bankId = $data['bankId'];
    $OperationSubject->taxId = $data['taxId'];
    $OperationSubject->name = $data['name'];
    $OperationSubject->currency = $data['currency'];

    $ContractReference = new \ContractReference();
    $ContractReference->id = $data['id'];
    $ContractReference->type = $data['type'];

    $Attributes = new \Attributes();
    if (!empty($data['card'])) {
        $Attributes->attrs['cardId'] = $data['card'];
    }

    switch ($SERVICE) {
        case "enroll":
            $SEP_TRANSFER = $IB->request("prefaceSEPTransferOperation", [
                $IB->TOKEN,
                $OperationSubject,
                isset($data['destinationDescription']) ? $data['destinationDescription'] : "",
                $amountInCents,
                $ContractReference,
                $Attributes
            ]);

            $SERVICE = $SEP_TRANSFER ? (($SEP_TRANSFER->status == 0) ? "commit" : $SERVICE) : "form";
            break;

        case "commit":
            $SEP_TRANSFER = $IB->request("executeSEPTransferOperation", [
                $IB->TOKEN,
                $OperationSubject,
                isset($data['destinationDescription']) ? $data['destinationDescription'] : "",
                $amountInCents,
                $ContractReference,
                $Attributes
            ]);

            if ($SEP_TRANSFER){
                switch ($SEP_TRANSFER->status){
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
            $FinancialOperationSubject -> subjectSEP = $OperationSubject;
            $FinancialOperationSubject -> subjectDescription = $data['destinationDescription'];
            include "cabinet_remittance_template_process.php";
            break;

        case "operation":
            $data = array_merge(
                (array)$operation->srcContractRef,
                ['destinationDescription' => $operation -> description],
                (array)$operation->subject->subjectSEP,
                ["cardId" => $data['card']],
                ['amountInCents' => $operation->amountInCents],
                ['finance_type' => $data['finance_type']]
            );
            $SERVICE = "form";
            break;
    }

    $EXCEPTION = $IB->EXCEPTION_process();
}
$financialOperationResult = $SEP_TRANSFER;
?>


