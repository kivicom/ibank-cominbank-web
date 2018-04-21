<?php
$FinancialOperation = strtr(basename(__FILE__, '.php'), array("cabinet_remittance_" => ""));

$IntrabankTransferOperation = false;

if ($SERVICE !== false) {
    $IntrabankOperationSubject = new \IntrabankOperationSubject();
    $IntrabankOperationSubject -> identifierType = $data['identifier'] ? ((strlen($data['identifier']) == 16) ? 3 : 2) : 0;
    $IntrabankOperationSubject -> identifier = $data['identifier'];
    $IntrabankOperationSubject -> currency = $data['currency'];
    $IntrabankOperationSubject -> challengeResponseValue = isset($data['challengeRequestValue']) ? $data['challengeRequestValue'] : null;

    $ContractReference = new \ContractReference();
    $ContractReference->id = $data['id'];
    $ContractReference->type = $data['type'];

    $Attributes = new \Attributes();
    if (!empty($data['card'])) {
        $Attributes->attrs['cardId'] = $data['card'];
    }

    switch ($SERVICE) {
        case "enroll":
            $IntrabankTransferOperation = $IB->request("prefaceIntrabankTransferOperation", [
                $IB->TOKEN,
                $IntrabankOperationSubject,
                $amountInCents,
                $ContractReference,
                $Attributes
            ]);
            $SERVICE = $IntrabankTransferOperation ? (($IntrabankTransferOperation->status == 0) ? "commit" : $SERVICE) : "form";
            break;

        case "commit":
            $IntrabankTransferOperation = $IB->request("executeIntrabankTransferOperation", [
                $IB->TOKEN,
                $IntrabankOperationSubject,
                $amountInCents,
                $ContractReference,
                $Attributes
            ]);

            if ($IntrabankTransferOperation){
                switch ($IntrabankTransferOperation->status){
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
            $FinancialOperationSubject -> intrabankOperationSubject = $IntrabankOperationSubject;

            include "cabinet_remittance_template_process.php";
            break;

        case "operation":
            $data = array_merge(
                (array)$operation->srcContractRef,
                (array)$operation->subject->intrabankOperationSubject,
                ["cardId" => $data['card']],
                ['amountInCents' => $operation->amountInCents],
                ['finance_type' => $data['finance_type']]
            );
            $SERVICE = "form";
            break;
    }

    $EXCEPTION = $IB->EXCEPTION_process();
}

$financialOperationResult = $IntrabankTransferOperation;

?>


