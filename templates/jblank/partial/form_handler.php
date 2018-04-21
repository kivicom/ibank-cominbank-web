<?php
if($_REQUEST){
    $jsonArray = $_REQUEST;
    $token = $jsonArray['token'];
    $returnUrl = $jsonArray['return_url'];
    $message = $jsonArray;
    unset ($message['token'], $message['return_url']);
    $messageString = '';
    foreach ($message as $paramenter => $value){
        $messageString .= ($messageString)? '\n' : '';
        $messageString .= $paramenter.':'.$value;
    }
    if(isset($I18N['{FORM-HANDLER-TOPIC-PRODUCT}'])){
        $subject = $I18N['{FORM-HANDLER-TOPIC-PRODUCT}'];
    }else{
        $subject = 'Заявка на продукт';
    }

    $sendMessage = $IB->request("sendMessage", [
        $token,
        $subject,
        $messageString
    ]);

    if ($IB -> EXCEPTION){
        if (isset($IB -> EXCEPTION -> errorMessageKey)) {
            $trans = "{EXCEPTION-".strtoupper(strtr($IB -> EXCEPTION -> errorMessageKey, array("_"=>"-")))."}";
            if (isset($I18N[$trans])) {
                $EXCEPTION = $I18N[$trans];
                $IB -> EXCEPTION = '';
            }
        }
        $errCode = 1;
    } elseif(in_array($sendMessage->operationInfo->status, array(0,3))) {  // если статус операции 0 или 3, то успешно
        $errCode = 0;
    } else{
        $errCode = 1;
    }

    header('Location: '.$returnUrl."?ErrCode=".$errCode);
    exit();
}