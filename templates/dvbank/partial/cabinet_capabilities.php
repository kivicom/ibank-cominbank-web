<?php

if($IB->userRole('full')) {

    $spentCards = 0;
    $spentAccount = 0;
    $spentCredit = 0;
    $spentTotal = 0;
    $saving = 0;
    $creditsTotal = 0;
    $creditsUsedTotal = 0;
    $cardsAvailable = 0;

    $rates = $IB->request("getCurrencyExchangeRateList", [
        $IB->TOKEN,
        array('USD', 'EUR', 'RUB', 'UAH'),
        'central_bank',
        $IB->SESS['TIME'] * 1000,
        new \Attributes()
    ]);
    if ($IB->EXCEPTION) {
        $IB->EXCEPTION = '';
    }
    if ($rates) {
        foreach ($rates as $index => $rate) {
            $rates[$rate->currency] = $rate->buyRate;
            unset($rates[$index]);
        }
    }

    //=========================
    $AllCard = $IB->request("findAllCardContracts", [
        $IB->TOKEN,
        new \Attributes()
    ]);

    if ($AllCard) {
        foreach ($AllCard as $accounts) {
            $spentCards += (
                isset($accounts->balance) &&
                isset($accounts->mainAccountCurrency) &&
                isset($accounts->creditLimit) &&
                isset($accounts->usedCreditLimit) &&
                isset($rates[$accounts->mainAccountCurrency])
            ) ? (
                ($accounts->creditLimit ? ($accounts->creditLimit - $accounts->usedCreditLimit) : $accounts->balance) *
                $rates[$accounts->mainAccountCurrency]
            ) : 0;

            $creditsTotal += (
                isset($accounts->creditLimit) &&
                isset($accounts->usedCreditLimit)
            ) ? (
                ($accounts->creditLimit ? ($accounts->creditLimit) : 0) *
                $rates[$accounts->mainAccountCurrency]
            ) : 0;

            $creditsUsedTotal += (
                isset($accounts->creditLimit) &&
                isset($accounts->usedCreditLimit)
            ) ? (
                ($accounts->creditLimit ? ($accounts->usedCreditLimit) : 0) *
                $rates[$accounts->mainAccountCurrency]
            ) : 0;

            if ($accounts->creditLimit == 0) {
                $cardsAvailable += $accounts->balance * $rates[$accounts->mainAccountCurrency];
            }
        }
    }


    //=========================
    $AllAccount = $IB->request("findAllAccountContracts", [
        $IB->TOKEN,
        new \Attributes()
    ]);

    if ($AllAccount) {
        foreach ($AllAccount as $accounts) {
            $spentAccount += (
                isset($accounts->balance) &&
                isset($accounts->mainAccountCurrency) &&
                isset($rates[$accounts->mainAccountCurrency])
            ) ? ($accounts->balance * $rates[$accounts->mainAccountCurrency]) : 0;
        }
    }

    //=========================
    $AllCredit = $IB->request("findAllCreditContracts", [
        $IB->TOKEN,
        new \Attributes()
    ]);

    if ($AllCredit) {
        foreach ($AllCredit as $accounts) {
            $spentCredit += (
                isset($accounts->balance) &&
                isset($accounts->mainAccountCurrency) &&
                isset($rates[$accounts->mainAccountCurrency])
            ) ? ($accounts->balance * $rates[$accounts->mainAccountCurrency]) : 0;
        }
        $creditsTotal += $spentCredit;
        $creditsUsedTotal += $spentCredit;
    }

    //=========================
    $AllDeposit = $IB->request("findAllDepositContracts", [
        $IB->TOKEN,
        new \Attributes()
    ]);

    if ($AllDeposit) {
        foreach ($AllDeposit as $accounts) {
            $saving += (
                isset($accounts->balance) &&
                isset($accounts->mainAccountCurrency) &&
                isset($rates[$accounts->mainAccountCurrency])
            ) ? ($accounts->balance * $rates[$accounts->mainAccountCurrency]) : 0;
        }
    }

    //=========================
    $spentTotal = $spentCards + $spentAccount + $spentCredit;

    switch ($type) {
        case "spent":
            ?>
            <div class="col-md-4 col-sm-6 col-xs-12 expand-container">
                <div class="overview expand-block selected">
                    <div class="overview__inner">

                        <div class="overview__title">
                            <i class="icons">
                                <!--                    <img src="/dev/static/img/content/visa.svg">-->
                            </i>
                            <span class="title__name">{CABINET-CAPABILITIES-SPEND}</span>
                        </div>
                        <div class="overview__data expand" data-height="50" data-event="hover">
                            <div class="data">
                                <span class="overview__val"><?= $tpl->priceFormat($spentTotal) ?></span>
                                <span class="overview__currency"> {UAH}</span>
                            </div>

                            <ul class="overview-prod__list col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <li class="overview-prod__item col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                    <div class="overview__wrap overview__wrap_det">
                                        <span class="overview__title overview__title_det">{CABINET-CAPABILITIES-ACCOUNTS}</span>
                                        <span class="overview__val"><?= $tpl->priceFormat($spentAccount) ?></span>
                                        <span class="overview__currency"> {UAH}</span>
                                    </div>
                                </li>
                                <li class="overview-prod__item  col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                    <div class="overview__wrap overview__wrap_det">
                                        <span class="overview__title overview__title_det">{CABINET-CAPABILITIES-CARDS}</span>
                                        <span class="overview__val"><?= $tpl->priceFormat($spentCards) ?></span>
                                        <span class="overview__currency"> {UAH}</span>
                                    </div>
                                </li>
                                <li class="overview-prod__item  col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                    <div class="overview__wrap overview__wrap_det">
                                        <span class="overview__title overview__title_det">{CABINET-CAPABILITIES-CREDITLIMIT}</span>
                                        <span class="overview__val"><?= $tpl->priceFormat($spentCredit) ?></span>
                                        <span class="overview__currency"> {UAH}</span>
                                    </div>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
            <?php
            break;
        case "saving":
            ?>
            <div class="col-md-4 col-sm-6 col-xs-12 expand-container">
                <div class="overview expand-block selected">
                    <div class="overview__inner">

                        <div class="overview__title">
                            <i class="icons">
                                <!--                    <img src="/dev/static/img/content/visa.svg">-->
                            </i>
                            <span class="title__name">{CABINET-CAPABILITIES-SAVING}</span>
                        </div>
                        <div class="overview__data expand" data-height="50" data-event="hover">
                            <div class="data">
                                <span class="overview__val"><?= $tpl->priceFormat($spentAccount + $cardsAvailable + $saving) ?></span>
                                <span class="overview__currency"> {UAH}</span>
                            </div>

                            <ul class="overview-prod__list col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <li class="overview-prod__item col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                    <span class="overview__title overview__title_det"></span>
                                    <div class="overview__wrap overview__wrap_det">
                                        <span class="overview__title overview__title_det">{CABINET-CAPABILITIES-ACCOUNTS}</span>
                                        <span class="overview__val"><?= $tpl->priceFormat($spentAccount) ?></span>
                                        <span class="overview__currency"> {UAH}</span>
                                    </div>
                                </li>
                                <li class="overview-prod__item col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                    <span class="overview__title overview__title_det"></span>
                                    <div class="overview__wrap overview__wrap_det">
                                        <span class="overview__title overview__title_det">{CABINET-CAPABILITIES-DEBITCARDS}</span>
                                        <span class="overview__val"><?= $tpl->priceFormat($cardsAvailable) ?></span>
                                        <span class="overview__currency"> {UAH}</span>
                                    </div>
                                </li>
                                <li class="overview-prod__item col-lg-4 col-md-4 col-sm-4 col-xs-4">
                                    <span class="overview__title overview__title_det"></span>
                                    <div class="overview__wrap overview__wrap_det">
                                        <span class="overview__title overview__title_det">{CABINET-CAPABILITIES-DEPOSITS}</span>
                                        <span class="overview__val"><?= $tpl->priceFormat($saving) ?></span>
                                        <span class="overview__currency"> {UAH}</span>
                                    </div>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
            <?php

            break;

        case "obligations":
            ?>
            <div class="col-md-4 col-sm-6 col-xs-12 expand-container">
                <div class="overview expand-block selected">
                    <div class="overview__inner">

                        <div class="overview__title danger">
                            <i class="icons">
                                <!--                    <img src="/dev/static/img/content/visa.svg">-->
                            </i>
                            <span class="title__name">{CABINET-CAPABILITIES-OBLIGATIONS}</span>
                        </div>
                        <div class="overview__data expand" data-height="50" data-event="hover">
                            <div class="data data_danger">
                                <span class="overview__val"><?= $tpl->priceFormat($creditsUsedTotal) ?></span>
                                <span class="overview__currency"> {UAH}</span>
                            </div>

                            <ul class="overview-prod__list">
                                <li class="overview-prod__item col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="overview__wrap overview__wrap_det">
                                        <span class="overview__title overview__title_det">{CABINET-CAPABILITIES-CREDITS}</span>
                                        <span class="overview__val"><?= $tpl->priceFormat($creditsTotal) ?></span>
                                        <span class="overview__currency"> {UAH}</span>
                                    </div>
                                </li>
                                <li class="overview-prod__item col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="overview__wrap overview__wrap_det">
                                        <span class="overview__title overview__title_det">{CABINET-CAPABILITIES-CREDIT-USED}</span>
                                        <span class="overview__val"><?= $tpl->priceFormat($creditsUsedTotal) ?></span>
                                        <span class="overview__currency"> {UAH}</span>
                                    </div>
                                </li>

                            </ul>
                        </div>

                    </div>
                </div>
            </div>
            <?php

            break;
    }
}
?>
