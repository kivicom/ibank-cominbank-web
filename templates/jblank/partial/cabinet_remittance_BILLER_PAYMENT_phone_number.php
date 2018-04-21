<?php
if(!empty($data['billerId']) && !empty($data['providerId'])){
    $findBillerById = $IB->request("findBillerById", [
        $IB->TOKEN,
        $data['providerId'],
        $data['billerId']
    ]);

    // для пополнения телефона первый и единственный параметр используем
    if(isset($data['phone_number']) or isset($data[$findBillerById->parametersConf[0]->name])){
        if(!empty($findBillerById->parametersConf)){
            $parameterName = $findBillerById->parametersConf[0]->name;
            $data[$parameterName] = $getParameters[$parameterName] = isset($data[$parameterName]) ? $data[$parameterName] : $data['phone_number'];
            // принудительно обрезаем телефон до 10 символов без +380
            if(in_array($SERVICE, array('commit','enroll', 'preface','new_template')) && $findBillerById->parametersConf[0]->type === 5){
                $dataAttrs[$parameterName] = substr(preg_replace("/[^0-9]/", "", $data[$parameterName] ), -10);
            }
        }
    }
    // обработка antiXSS
    if(!empty($findBillerById->parametersConf) && in_array($SERVICE, array('commit','enroll', 'preface','new_template','edit_template'))){
        $antiXss = new AntiXSS();
        foreach ($findBillerById->parametersConf as $parameter) {
            if($parameter->type === 1 && !empty($data[$parameter->name])){
                $data[$parameter->name] = $getParameters[$parameter->name] = $dataAttrs[$parameter->name] = $antiXss->xss_clean($data[$parameter->name]);
            }
        }
    }
}
?>