<?php
$data = isset($_POST['data']) ? $_POST['data'] : (isset($_GET['data']) ? $_GET['data'] : false);

if(isset($type)){

    switch ($type){
        case 1:
            $method = 'findAllCreditContracts';
            $productTitle = '{CABINET-ACCOUNT-DETAIL-CREDIT}';
            $newAccountTitle = '{CABINET-ACCOUNT-DETAIL-NEWCREDIT}';
            $accountUrl = $tpl->lang.$IB -> CONFIG -> get('CABINET_CREDITS_URL')."?data[id]=";
            $balanceTitle = '{CABINET-ACCOUNT-DETAIL-DEBT}';
            switch ($tpl->lang){
                case 'uk':
                    $newAccountUrl = $IB -> CONFIG -> get('NEWCREDIT_URL_UA');
                    break;
                case 'ru':
                    $newAccountUrl = $IB -> CONFIG -> get('NEWCREDIT_URL_RU');
                    break;
            }
            break;
        case 2:
            $method = 'findAllAccountContracts';
            $productTitle = '{CABINET-ACCOUNT-DETAIL-ACCOUNT}';
            $newAccountTitle = '{CABINET-ACCOUNT-DETAIL-NEWACCOUNT}';
            $accountUrl = $tpl->lang.$IB -> CONFIG -> get('CABINET_ACCOUNTS_URL')."?data[id]=";
            $balanceTitle = '{CABINET-ACCOUNT-DETAIL-BALANCE}:';
            switch ($tpl->lang){
                case 'uk':
                    $newAccountUrl = $IB -> CONFIG -> get('NEWACCOUNT_URL_UA');
                    break;
                case 'ru':
                    $newAccountUrl = $IB -> CONFIG -> get('NEWACCOUNT_URL_RU');
                    break;
            }
            break;
        case 3:
            $method = 'findAllDepositContracts';
            $productTitle = '{CABINET-ACCOUNT-DETAIL-DEPOSIT}';
            $newAccountTitle = '{CABINET-ACCOUNT-DETAIL-NEWDEPOSIT}';
            $accountUrl = $tpl->lang.$IB -> CONFIG -> get('CABINET_DEPOSITS_URL')."?data[id]=";
            $balanceTitle = '{CABINET-ACCOUNT-DETAIL-BALANCE}:';
            switch ($tpl->lang){
                case 'uk':
                    $newAccountUrl = $IB -> CONFIG -> get('NEWDEPOSIT_URL_UA');
                    break;
                case 'ru':
                    $newAccountUrl = $IB -> CONFIG -> get('NEWDEPOSIT_URL_RU');
                    break;
            }
            break;
    }
    $AllAccounts = $IB -> request($method, [
        $IB->TOKEN,
        new \Attributes()
    ]);

    if(isset($data['id'])){
        $account = $data['id'];
    }else{
        if(isset($AllAccounts[0])){
            $account = isset($AllAccounts[0]->id) ? $AllAccounts[0]->id : 0;
        }
        else{
            $account = 0;
        }
    }

    if($account){
        $accountsList = array();
        foreach ($AllAccounts as $accountObjectItem){
            $accountsList[] = $accountObjectItem->id;
            if($accountObjectItem->id == $account){
                $accountObject = $accountObjectItem;
            }
        }

        if ($accountObject) {

            $getAuthSession = $IB -> request("getAuthSession", [
                $IB->TOKEN
            ]);
            $userLevel = $getAuthSession->level;

            $pos = array_search($account, $accountsList);
            $leftAccountUrl = isset($accountsList[$pos - 1]) ? $accountUrl.$accountsList[$pos - 1] : FALSE;
            $rightAccountUrl = isset($accountsList[$pos + 1]) ? $accountUrl.$accountsList[$pos + 1] : FALSE;

            $mainAccountCurrency = isset($accountObject -> mainAccountCurrency) ? $accountObject -> mainAccountCurrency : "";
            $accountCurrency = '{'.$mainAccountCurrency.'}';
            $accountBalance = isset($accountObject -> balance) ? $tpl->priceFormat($accountObject -> balance) : 0;

            $productTitle = !empty($accountObject->productTitle) ? $accountObject->productTitle : $productTitle;
            ?>

            <div class="product-info__slaider">
                <a href="<?=$leftAccountUrl?>" class="<?php if(!$leftAccountUrl){ ?>not-visible<?php } ?> slaider-button col-md-2 col-sm-2 col-xs-1">
                    <span class="hidden-xs">{CABINET-ACCOUNT-DETAIL-PREV}</span>
                </a>

                <div class="info  col-md-8 col-sm-8 col-xs-9">
                    <span class="title"><?php echo $accountObject->legalNumber ?></span>
                    <?php if(!empty($accountObject->startDate)){ ?><span class="date">{FROM} <?=$accountObject->startDate?></span><?php } ?>
                </div>

                <?php if($rightAccountUrl){ ?>
                    <a href="<?=$rightAccountUrl?>" class="slaider-button slaider-button_reverse col-md-2 col-sm-2 col-xs-1">
                        <span class="hidden-xs">{CABINET-ACCOUNT-DETAIL-NEXT}</span>
                    </a>
                <?php } ?>
            </div>

            <ul class="product-info__buttons">
                <!-- <li class="btn-white button-with-options">
                    {CABINET-ACCOUNT-DETAIL-ACCOUNTCONTROL}
                    <ul class="list-with-options hide">
                        <li>
                            <a class="block-card-action">{CABINET-ACCOUNT-DETAIL-REQUISITES}</a>
                        </li>
                        <li>
                            <a href="account-conditions-action">{CABINET-ACCOUNT-DETAIL-CONDITIONS}</a>
                        </li>
                    </ul>
                </li> -->

                <li>
                    <a href="<?=$newAccountUrl?>" class="">
                        <button class="btn-green"><?=$newAccountTitle?></button>
                    </a>
                </li>


            </ul>


            <div class="product-info panel-card">

                <div class="product_info__title"><?=$productTitle?></div>
                <div class="product-info__number">
                    <div class="title">{CABINET-ACCOUNT-DETAIL-ACCOUNTNUMBER}:</div>
                    <div class="number"><?=$accountObject->mainAccountNumber?></div>
                </div>

                <div class="product-info__balance">
                    <div class="title"><?=$balanceTitle?></div>
                    <div class="balance"><?=$accountBalance?> <?=$accountCurrency?></div>
                </div>
            </div>

            <ul class="product-info__buttons">
                <li class="btn-white button-with-options btn_refill">
                    <?php if($type == 1) { ?>
                    {CABINET-ACCOUNT-DETAIL-PAYCREDIT}
                    <?php } else { ?>
                    {CABINET-CARD-DETAIL-REFILL}
                    <?php } ?>
                    <ul class="list-with-options hide">
                        <li>
                            <a href="<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_REMITTANCE_URL')."?FinancialOperationType=1&data[id_to]=".(urlencode($account))."&data[type_to]=".$type)?>">{CABINET-CARD-DETAIL-FROM-OWN-ACCOUNT-CARD}</a>
                        </li>
                        <li>
                            <a href="<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_REMITTANCE_URL')."?FinancialOperationType=10&data[id_to]=".(urlencode($account))."&data[type_to]=".$type)?>">{CABINET-CARD-DETAIL-FROM-OTHERCARD}</a>
                        </li>
                    </ul>
                </li>
                <?php if($type != 1) { ?>
                <li class="btn-white button-with-options btn_transfer">
                    {CABINET-CARD-DETAIL-TRANSFER}
                    <ul class="list-with-options hide">
                        <?php if(in_array($type,array(2,3))) { ?>
                        <li>
                            <a href="<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_REMITTANCE_URL')."?FinancialOperationType=1&data[id]=".(urlencode($account))."&data[type]=".$type)?>">{CABINET-CARD-DETAIL-C2C}</a>
                        </li>
                        <li>
                            <a href="<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_REMITTANCE_URL')."?FinancialOperationType=8&data[id]=".(urlencode($account))."&data[type]=".$type)?>">{CABINET-CARD-DETAIL-P2P}</a>
                        </li>
                        <?php } ?>
                        <?php if(in_array($type,array(2))) { ?>
                        <li>
                            <a href="<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_REMITTANCE_URL')."?FinancialOperationType=2&data[id]=".(urlencode($account))."&data[type]=".$type)?>">{CABINET-CARD-DETAIL-SEP}</a>
                        </li>
                        <?php } ?>
                    </ul>
                </li>
                <?php } ?>
                <?php if(in_array($type,array(2))) { ?>
                <li class="btn-white--pay">
                    <a href="/<?= ($tpl->lang . $IB->CONFIG->get('CABINET_REMITTANCE_URL')) ?>/?FinancialOperationType=3">
                        <button class="btn-white">{CABINET-CARD-DETAIL-PAY}</button>
                    </a>
                </li>
                <?php } ?>

            </ul>

            [cabinet_operations account="<?=$account?>" type="<?=$type?>" tabs="finance"][/cabinet_operations]

            <?php

        }
    }
}

?>



