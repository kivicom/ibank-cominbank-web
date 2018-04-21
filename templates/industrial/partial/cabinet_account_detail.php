<?php
if(isset($type)){

    switch ($type){
        case 1:
            $method = 'findAllCreditContracts';
            $productTitle = '{CABINET-ACCOUNT-DETAIL-CREDIT}';
            $newAccountTitle = '{CABINET-ACCOUNT-DETAIL-NEWCREDIT}';
            $accountUrl = $tpl->lang.$IB -> CONFIG -> get('CABINET_CREDITS_URL')."?account=";
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
            $accountUrl = $tpl->lang.$IB -> CONFIG -> get('CABINET_ACCOUNTS_URL')."?account=";
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
            $accountUrl = $tpl->lang.$IB -> CONFIG -> get('CABINET_DEPOSITS_URL')."?account=";
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

    if(isset($_GET['account'])){
        $account = $_GET['account'];
    }else{
        if(isset($AllAccounts[0])){
            $account = isset($AllAccounts[0]->id) ? $AllAccounts[0]->id : 0;
        }
        else{
            $account = 0;
        }
    }

    if($account && $AllAccounts){
        $accountObject = false;

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

            $operations = [];
            $operations[($type == 1) ? '{CABINET-ACCOUNT-DETAIL-PAYCREDIT}' : '{CABINET-CARD-DETAIL-REFILL}'] = [
                '{CABINET-CARD-DETAIL-FROM-OWN-ACCOUNT-CARD}' => '/'.($tpl->lang.$IB -> CONFIG -> get('CABINET_REMITTANCE_URL')."?FinancialOperationType=1&data[id]=".(urlencode($account))."&data[type]=".$type."&data[DIRECT]=to"),
                '{CABINET-CARD-DETAIL-FROM-OTHERCARD}' => '/'.($tpl->lang.$IB -> CONFIG -> get('CABINET_REMITTANCE_URL')."?FinancialOperationType=10&data[id]=".(urlencode($account))."&data[type]=".$type)
            ];

            if ($type != 1) {
                if (in_array($type,array(2,3))) {
                    $operations['{CABINET-CARD-DETAIL-TRANSFER}'] = [
                        '{CABINET-CARD-DETAIL-C2C}' => '/'.($tpl->lang.$IB -> CONFIG -> get('CABINET_REMITTANCE_URL')."?FinancialOperationType=1&data[id]=".(urlencode($account))."&data[type]=".$type),
                        '{CABINET-CARD-DETAIL-P2P}' => '/'.($tpl->lang.$IB -> CONFIG -> get('CABINET_REMITTANCE_URL')."?FinancialOperationType=8&data[id]=".(urlencode($account))."&data[type]=".$type)
                    ];
                }

                if (in_array($type,array(2))) {
                    $operations['{CABINET-CARD-DETAIL-TRANSFER}']['{CABINET-CARD-DETAIL-SEP}'] = '/'.($tpl->lang.$IB -> CONFIG -> get('CABINET_REMITTANCE_URL')."?FinancialOperationType=2&data[id]=".(urlencode($account))."&data[type]=".$type);
                }
            }

            if ($type != 1) {
                $operations['{CABINET-CARD-DETAIL-PAY}'] = '/cabinet/oplata-posluh';
            }

            $operations[$newAccountTitle] = $newAccountUrl;

            $props = [
                'title' => $productTitle,
                'account' => $accountObject->legalNumber,
                'date_from' => (!empty($accountObject->startDate)) ? '{FROM} '.$accountObject->startDate : '',
                'list' => [
                    '{CABINET-ACCOUNT-DETAIL-ACCOUNTNUMBER}' => $accountObject->mainAccountNumber,
                ],
                'status' => $balanceTitle,
                'balance' => $accountBalance.' '.$accountCurrency,
                'operations' => $operations,
                'prev' => $leftAccountUrl ? $leftAccountUrl : '',
                'next' => $rightAccountUrl ? $rightAccountUrl : ''
            ];

            include $tpl -> pathFullTemplate."/snippets/account-info.php";
            ?>

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
                    <a href="<?=$newAccountUrl?>">
                        <button class="btn-green"><?=$newAccountTitle?></button>
                    </a>
                </li>


            </ul>

            [cabinet_operations account="<?=$account?>" type="<?=$type?>" tabs="finance"][/cabinet_operations]

            <?php

        }
    }
}

?>



