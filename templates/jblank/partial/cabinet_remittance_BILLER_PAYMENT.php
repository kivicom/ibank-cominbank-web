<?php
$FinancialOperation = strtr(basename(__FILE__, '.php'), array("cabinet_remittance_" => ""));
$SERVICE = $SERVICE ? $SERVICE : (($data['billerId'] && $data['providerId']) ? "form" : "categories");

$BILLER_PAYMENT = false;

if ($SERVICE !== false) {

    if(isset($operation)){
        $data['billerId'] = $operation->attributes->attrs['billerId'];
        $data['providerId'] = $operation->subject->subjectBiller->providerId;
    }
    $dataAttrs = $data;

    include "cabinet_remittance_BILLER_PAYMENT_phone_number.php";

    if(!empty($dataAttrs['card'])){
      $dataAttrs['cardId'] = $dataAttrs['card'];
    }


    $Attributes = new \Attributes;
    $Attributes->attrs = $dataAttrs;

    $BillerOperationSubject = new \BillerOperationSubject;
    $BillerOperationSubject->billerId = $data['billerId'];
    $BillerOperationSubject->billerAccount = current($data);
    $BillerOperationSubject->providerId = $data['providerId'];
    $BillerOperationSubject->parameters = $Attributes;

    $ContractReference = new \ContractReference;
    $ContractReference->id = $data['id'];
    $ContractReference->type = $data['type'];



    switch ($SERVICE) {
        case "detection":
            switch ($data['detection_type']) {
                case "mobile":
                    $phone = isset($data['phone_number']) ? $data['phone_number'] : false;
                    $amountInCents = isset($data['amountInCents']) ? $data['amountInCents'] : false;
                    $account_id = $data['id'];
                    $card = isset($data['card']) ? $data['card'] : false;
                    $type = $data['type'];
                    $phone_number = $data['phone_number'];
                    $provider = false;
                    $id = false;

                    if ($phone && $amountInCents) {
                        $phone = "+".preg_replace('/\D/', '', $phone);
                        $amountInCents = preg_replace('/\D/', '', $amountInCents);

                        $xml = simplexml_load_file($IB -> CONFIG -> get('BILLERS_AUTODETECT_URL'));
                        $detection = $xml -> detection;
                        foreach ($detection -> biller as $biller) {
                            foreach ($biller -> match as $match) {
                                $regexp = (string) $match['regexp'];
                                if (preg_match($regexp, $phone)) {
                                    $provider = (string) $biller['provider'];
                                    $id = (string) $biller['id'];
                                    break;
                                }
                            }
                            if (($provider !== false) && ($id !== false)) break;
                        }
                    }

                    if (($provider !== false) && ($id !== false)) {
                        header('Location: /'.$tpl->lang . $IB->CONFIG->get('CABINET_REMITTANCE_URL').'?FinancialOperationType='.$FinancialOperationTypeActive.'&data[billerId]='.urlencode($id).'&data[providerId]='.urlencode($provider)
                        . '&data[amountInCents]='.$amountInCents . '&data[id]='.$account_id . '&data[card]='.$card . '&data[type]='.$type . '&data[phone_number]='.$phone_number) ;
                        exit();
                    } else {
                        header('Location: /'.$tpl->lang . $IB->CONFIG->get('CABINET_REMITTANCE_URL').'?FinancialOperationType='.$FinancialOperationTypeActive);
                        exit();
                    }
                    break;
            }
            break;

        case "enroll":
            $BILLER_PAYMENT = $IB->request("prefaceBillPaymentOperation", [
                $IB->TOKEN,
                $BillerOperationSubject,
                $amountInCents,
                $ContractReference
            ]);

            $SERVICE = $BILLER_PAYMENT ? (($BILLER_PAYMENT->status == 0) ? "commit" : $SERVICE) : "form";

            break;
        case "commit":
            $BILLER_PAYMENT = $IB->request("executeBillPaymentOperation", [
                $IB->TOKEN,
                $BillerOperationSubject,
                $amountInCents,
                $ContractReference
            ]);

            if ($BILLER_PAYMENT){
                switch ($BILLER_PAYMENT->status){
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
            $FinancialOperationSubject->type = $FinancialOperationTypeActive;
            $FinancialOperationSubject->subjectBiller = $BillerOperationSubject;

            include "cabinet_remittance_template_process.php";
            break;

        case "operation":

            $data = array_merge(
                (array)$operation->attributes->attrs,
                ["cardId" => $data['card']],
                ['amountInCents' => $operation->amountInCents],
                ['finance_type' => $data['finance_type']]
            );
            $data['billerId'] = $operation->attributes->attrs['billerId'];
            $data['providerId'] = $operation->subject->subjectBiller->providerId;
            $SERVICE = "form";
            break;
    }

    $EXCEPTION = $IB->EXCEPTION_process();
}

$financialOperationResult = $BILLER_PAYMENT;

?>


