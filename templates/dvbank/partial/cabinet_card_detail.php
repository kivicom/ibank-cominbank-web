<?php
$operationHistoryFilter = new \operationHistoryFilter();

$AllCard = $IB -> request("findAllCardContracts", [
    $IB->TOKEN,
    new \Attributes()
]);
if(isset($AllCard[0])){
    $defaultAccount = $AllCard[0]->id;
    if(isset($AllCard[0]->cards[0])){
        $defaultCardId = $AllCard[0]->cards[0]->id;
    }
    else{
        $defaultCardId = 0;
    }
}
else{
    $defaultAccount = 0;
    $defaultCardId = 0;
}

$account = isset($_GET['account']) ? $_GET['account'] : $defaultAccount;
$cardId = isset($_GET['card']) ? $_GET['card'] : $_GET['card'] = $defaultCardId;

if($account && $cardId){
    $detail = $IB -> request("fetchCardContractDetails", [
        $IB->TOKEN,
        $account,
        new \Attributes()
    ]);

    if ($detail && $AllCard) {
        $listCards = array();
        foreach ($AllCard as $CardItem) {
            foreach ($CardItem -> cards as $cards) {
                if (isset($CardItem -> id) && isset($cards -> id)) {
                    $listCards[] = $CardItem -> id.",".$cards -> id;
                }
            }
        }

        if (isset($detail -> cards) && is_array($detail -> cards)) {
            foreach ($detail -> cards as $card) {
                if (isset($card -> id) && ($cardId == $card -> id)) {

                    $getAuthSession = $IB -> request("getAuthSession", [
                        $IB->TOKEN
                    ]);
                    $userLevel = $getAuthSession->level;

                    $cardStatus = $card->status;
                    // блокировка карты
                    if (isset($_GET['account']) && isset($_GET['card']) && isset($_GET['action'])){
                        switch ($_GET['action']){
                            case 'lock':
                                $actionMethod = "lockCard";
                                break;
                            case 'unlock':
                                $actionMethod = "unlockCard";
                                break;
                        }
                        $cardAction = $IB -> request($actionMethod, [
                            $IB->TOKEN,
                            $account,
                            $card->cardNumberMask,
                            new \Attributes()
                        ]);

                        if ($IB -> EXCEPTION){
                            if (isset($IB -> EXCEPTION -> errorMessageKey)) {
                                $trans = "{EXCEPTION-".strtoupper(strtr($IB -> EXCEPTION -> errorMessageKey, array("_"=>"-")))."}";
                                if (isset($I18N[$trans])) {
                                    $EXCEPTION = $I18N[$trans];
                                    $IB -> EXCEPTION = '';
                                }
                            }
                        } else {
                            header('Location: /'.$tpl->lang.$IB -> CONFIG -> get('CABINET_CARDS_URL')."?account=".(urlencode($account)."&card=".(urlencode($cardId))));
                            exit();
                        }

                        // на основе статуса операции (0 - success )определяем статус карты
                        if (empty($cardAction->status)){
                            switch ($_GET['action']){
                                case 'lock':
                                    $cardStatus = 0;
                                    break;
                                case 'unlock':
                                    $cardStatus = 1;
                                    break;
                            }
                        }
                    }

                    $cardCV = isset($card->settings->cardNotPresentCodeVerificationEnabled) ? $card->settings->cardNotPresentCodeVerificationEnabled : false;
                    // включение/отключение проверки CVV-кода
                    if (isset($_GET['account']) && isset($_GET['card']) && isset($_GET['cvaction'])){
                        $CardSettings = new \CardSettings();
                        $CardSettings->motoOperationsEnabled = !empty($card->settings) ? $card->settings->motoOperationsEnabled : FALSE;
                        $CardSettings->ecommerceOperationsEnabled = !empty($card->settings) ? $card->settings->ecommerceOperationsEnabled : FALSE;
                        switch ($_GET['cvaction']){
                            case 'disable':
                                $CardSettings->cardNotPresentCodeVerificationEnabled = FALSE;
                                break;
                            case 'enable':
                                $CardSettings->cardNotPresentCodeVerificationEnabled = TRUE;
                                break;
                        }
                        $ContractReference = new \ContractReference;
                        $ContractReference->id = $detail->id; // айди счета
                        $ContractReference->type = 4; // тип - карта
                        $cardAction = $IB -> request('changeCardSettings', [
                            $IB->TOKEN,
                            $ContractReference,
                            $card->cardNumberMask,
                            $CardSettings,
                            new \Attributes()
                        ]);
                        $EXCEPTION = $IB->EXCEPTION_process();
                        if (!empty($cardAction) && $cardAction->status != 0){
                            $EXCEPTION = '{CABINET-CARD-DETAIL-CV-ERROR}';
                        }
                        if(!$EXCEPTION){
                            header('Location: /'.$tpl->lang.$IB -> CONFIG -> get('CABINET_CARDS_URL')."?account=".(urlencode($account)."&card=".(urlencode($cardId))));
                            exit();
                        }
                    }
                    $cardNumberMask = $card -> cardNumberMask;
                    $MasterVisa = substr($cardNumberMask, 0, 1);
                    switch ($MasterVisa){
                        case 4:
                            $MasterVisaTitle = '{CABINET-CARD-DETAIL-VISACARD}';
                            break;
                        case 5:
                            $MasterVisaTitle = '{CABINET-CARD-DETAIL-MASTERCARD}';
                            break;
                    }
                    //$productTitle = isset($detail -> productTitle) ? ($detail -> productTitle ? $detail -> productTitle : $MasterVisaTitle ) : $MasterVisaTitle;
                    $productTitle = $MasterVisaTitle;
                    $creditLimit = isset($detail -> creditLimit) ? $detail -> creditLimit : 0;
                    $usedCreditLimit = isset($detail -> usedCreditLimit) ? $detail -> usedCreditLimit : 0;

                    //$available = $creditLimit - $usedCreditLimit;
                    $available = $creditLimit;
                    $available = isset($detail -> balance) ? ($available + $detail -> balance) : $creditLimit;

                    $creditLimit = $creditLimit ? $tpl->priceFormat($creditLimit) : 0;
                    $usedCreditLimit = $usedCreditLimit ? $tpl -> priceFormat($usedCreditLimit) : 0;
                    $available = $available ? $tpl -> priceFormat($available) : 0;

                    $mainAccountCurrency = isset($detail -> mainAccountCurrency) ? $detail -> mainAccountCurrency : "";
                    $cardCurrency = '{'.$mainAccountCurrency.'}';
                    $balance = isset($detail -> balance) ? $tpl->priceFormat($detail -> balance) : 0;

                    $mainAccountNumber = isset($detail -> mainAccountNumber) ? $detail -> mainAccountNumber : "";

                    $leftCardUrl = $detail -> id.",".$card ->id;
                    if (false !== ($pos = array_search($leftCardUrl, $listCards))) {
                        if (isset($listCards[$pos - 1])) {
                            $leftCardUrl = explode(",",$listCards[$pos - 1]);
                            $leftCardUrl = $tpl->lang.$IB -> CONFIG -> get('CABINET_CARDS_URL')."?account=".$leftCardUrl[0]."&card=".$leftCardUrl[1];
                        } else {
                            $leftCardUrl = false;
                        }
                    }

                    $rightCardUrl = $detail -> id.",".$card ->id;
                    if (false !== ($pos = array_search($rightCardUrl, $listCards))) {
                        if (isset($listCards[$pos + 1])) {
                            $rightCardUrl = explode(",",$listCards[$pos + 1]);
                            $rightCardUrl = $tpl->lang.$IB -> CONFIG -> get('CABINET_CARDS_URL')."?account=".$rightCardUrl[0]."&card=".$rightCardUrl[1];
                        } else {
                            $rightCardUrl = false;
                        }
                    }

                    if(!empty($card->attributes->attrs['skin'])){
                        $cardImageSrc =  ($IB -> CONFIG -> get('CARDS_SKIN_DIR')).$card->attributes->attrs['skin'].'.png';
                    }else{
                        $cardImageSrc = $tpl->pathTemplate . "/images/cards/";
                        switch ($MasterVisa) {
                            case "4":
                                $cardImageSrc .= 'dvbank-visacard.png';
                                break;
                            case "5":
                                $cardImageSrc .= 'dvbank-mastercard.png';
                                break;
                        }
                    }

                    $limitsLink = "/" . $tpl->lang . ($IB -> CONFIG -> get('CABINET_CARDS_LIMIT')). "?account=" . $account . "&card=" . $cardId;

                    switch ($tpl->lang){
                        case 'uk':
                            $newCardUrl = $IB -> CONFIG -> get('NEWCARD_URL_UA');
                            break;
                        case 'ru':
                            $newCardUrl = $IB -> CONFIG -> get('NEWCARD_URL_RU');
                            break;
                    }
                    ?>

                    <div class="product-info__slaider">

                        <a href="<?=$leftCardUrl?>" class="<?php if(!$leftCardUrl){ ?>not-visible<?php } ?> slaider-button col-md-2 col-sm-2 col-xs-1">
                            <span class="hidden-xs">{CABINET-CARD-DETAIL-PREV}</span>
                        </a>

<!--                        <div class="info col-md-8 col-sm-8 col-xs-9 card_info_header">
-->                        <div class="info col-md-8 col-sm-8 col-xs-9 card_info_header">
<!--                            <div class="col-md-4 col-sm-4 col-xs-12 card_num_field">-->
                            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 card_num_field">
                                <div class="card_num_field__field_name">{CABINET-CARD-DETAIL-PAYMENTCARD}:</div>
                                <div class="card_num_field__card_num"><?=$cardNumberMask?></div>
                            </div>
<!--                            <div class="col-md-4 col-sm-4 col-xs-12 card_blnc_field">-->
                            <div class="col-lg-4 col-md-6 col-sm-6 col-xs-12 card_blnc_field">
                                <span class="title card_blnc_field__name">{CABINET-CARD-DETAIL-CARDBALANCE}</span>
                                <span class="date card_blnc_field__value"><?=($available." ".$cardCurrency)?></span>
                            </div>
<!--                            <div class="col-md-4 col-sm-4 col-xs-12 card_settings">-->
                            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12 card_settings">
<!--                                <div class="col-lg-12 col-md-6 col-sm-6 col-xs-12 text-center text-xs-center card_settings__on-off"><b>{CABINET-CARD-DETAIL-CARDSTATUS}</b> --><?//=($cardStatus)? "{CABINET-CARD-DETAIL-ACTIVE-CARD}": "{CABINET-CARD-DETAIL-BLOCKED-CARD}"?><!--</div>-->
                                <div class="col-lg-12 col-md-6 col-sm-6 col-xs-12 text-center text-xs-center card_settings__on-off"></b>{CABINET-CARD-DETAIL-CARDSTATUS} <b> <?=($cardStatus)? "{CABINET-CARD-DETAIL-ACTIVE-CARD}": "{CABINET-CARD-DETAIL-BLOCKED-CARD}"?></b></div>
<!--                                <div class="col-lg-12 col-md-6 col-sm-6 col-xs-12 text-center text-xs-center card_settings__CVV_check"><b>{CABINET-CARD-DETAIL-CV-STATUS}:</b> --><?//=($cardCV)? "{CABINET-CARD-DETAIL-CV-STATUS-ENABLE}": "{CABINET-CARD-DETAIL-CV-STATUS-DISABLE}"?><!--</div>-->
                                <div class="col-lg-12 col-md-6 col-sm-6 col-xs-12 text-center text-xs-center card_settings__CVV_check">{CABINET-CARD-DETAIL-CV-STATUS}: <b> <?=($cardCV)? "{CABINET-CARD-DETAIL-CV-STATUS-ENABLE}": "{CABINET-CARD-DETAIL-CV-STATUS-DISABLE}"?></b></div>
                            </div>
                        </div>
                        <?php if($rightCardUrl){ ?>
                            <a href="<?=$rightCardUrl?>" class="slaider-button slaider-button_reverse col-md-2 col-sm-2 col-xs-1">
                               <span class="hidden-xs">{CABINET-CARD-DETAIL-NEXT}</span>
                            </a>
                        <?php } ?>
                    </div>

                    <ul class="product-info__buttons">
                        <li class="btn-white button-with-options card_mngmnt">
                            {CABINET-CARD-DETAIL-CARDCONTROL}
                            <ul class="list-with-options hide">
                                <li>
                                    <a class="block-card-action" onclick="jQuery('#form-blockcard').submit(); return false;">
                                        <?=($cardStatus)? "{CABINET-CARD-DETAIL-BLOCK-CARD}": "{CABINET-CARD-DETAIL-UNBLOCK-CARD}"?>
                                    </a>
                                </li>
                                <li>
                                    <a class="card-limit" href="<?=$limitsLink?>">{CABINET-CARD-DETAIL-LIMIT}</a>
                                </li>
                                <li>
                                    <a class="cvv-card-action" onclick="jQuery('#form-cvv').submit();">
                                        <?=($cardCV)? "{CABINET-CARD-DETAIL-CV-DISABLE}": "{CABINET-CARD-DETAIL-CV-ENABLE}"?>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <li>
                            <a href="<?=$newCardUrl?>">
                                <button class="btn-green">{CABINET-CARD-DETAIL-NEWCARD}</button>
                            </a>
                        </li>


                    </ul>


                    <div class="product-info">

                        <figure>
                            <img src="<?=$cardImageSrc?>" alt=""/>
                            <figcaption>
                                <h2 class="product__title-hidden">Cardholders details</h2>
                                <div class="card_num_field__card_num card_num_field__card_num--mask">
                                    <p class="info__num"><?=$cardNumberMask?></p>
                                    <p class="info__date"><?=$card->expiryMonth.'/'.$card->expiryYear?></p>
                                    <p class="info__name"><?=$card->ownerName?></p>
                                </div>
                            </figcaption>
                        </figure>

                    </div>

                    <ul class="product-info__buttons card_footer">
                        <li class="btn-white button-with-options btn_refill">
                            {CABINET-CARD-DETAIL-REFILL}
                            <ul class="list-with-options hide">
                                <li>
                                    <a href="<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_REMITTANCE_URL')."?FinancialOperationType=1&data[id_to]=".(urlencode($account))."&data[type_to]=4&data[card_to]=$cardId")?>">{CABINET-CARD-DETAIL-FROM-OWN-ACCOUNT-CARD}</a>
                                </li>
                                <li>
                                    <a href="<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_REMITTANCE_URL')."?FinancialOperationType=10&data[id_to]=".(urlencode($account))."&data[type_to]=4&data[card_to]=$cardId")?>">{CABINET-CARD-DETAIL-FROM-OTHERCARD}</a>
                                </li>
                            </ul>
                        </li>
                        <li class="btn-white button-with-options btn_transfer">
                            {CABINET-CARD-DETAIL-TRANSFER}
                            <ul class="list-with-options hide">
                                <li>
                                    <a href="<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_REMITTANCE_URL')."?FinancialOperationType=1&data[id]=".(urlencode($account))."&data[type]=4&data[card]=$cardId")?>">{CABINET-CARD-DETAIL-C2C}</a>
                                </li>
                                <li>
                                    <a href="<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_REMITTANCE_URL')."?FinancialOperationType=8&data[id]=".(urlencode($account))."&data[type]=4&data[card]=$cardId")?>">{CABINET-CARD-DETAIL-P2P}</a>
                                </li>
                                <li>
                                    <a href="<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_REMITTANCE_URL')."?FinancialOperationType=2&data[id]=".(urlencode($account))."&data[type]=4&data[card]=$cardId")?>">{CABINET-CARD-DETAIL-SEP}</a>
                                </li>
                                <li>
                                    <a href="<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_REMITTANCE_URL')."?FinancialOperationType=9&data[id]=".(urlencode($account))."&data[type]=4&data[card]=$cardId")?>">{CABINET-CARD-DETAIL-CTC}</a>
                                </li>
                            </ul>
                        </li>
                        <li class="btn_pay">
                            <a href="/<?= ($tpl->lang . $IB->CONFIG->get('CABINET_REMITTANCE_URL')) ?>/?FinancialOperationType=3">
                                <button class="btn-white btn-white--pay">{CABINET-CARD-DETAIL-PAY}</button>
                            </a>
                        </li>
                    </ul>

                    <form id="form-blockcard" method="get" action="" class="validate" hidden>
                        <input type="hidden" name="account" value="<?=$account?>" />
                        <input type="hidden" name="card" value="<?=$cardId?>" />
                        <input type="hidden" name="action" value="<?=($cardStatus)? 'lock': 'unlock'?>" />
                        <?php if(!$userLevel){ ?>
                        <input class="system_ext_auth_required required" type="hidden" value="" />
                        <?php } ?>
                    </form>

                    <form id="form-cvv" method="get" action="" class="validate" hidden>
                        <input type="hidden" name="account" value="<?=$account?>" />
                        <input type="hidden" name="card" value="<?=$cardId?>" />
                        <input type="hidden" name="cvaction" value="<?=($cardCV)? 'disable': 'enable'?>" />
                        <?php if(!$userLevel){ ?>
                            <input class="system_ext_auth_required required" type="hidden" value="" />
                        <?php } ?>
                    </form>

                    [cabinet_operations account="<?=$account?>" type="4" tabs="finance"][/cabinet_operations]

                    <?php

                    break;
                }
            }
        }
    }
}


?>