<?php
$spentCards = 0;
$spentAccount = 0;
$spentCredit = 0;
$spentTotal = 0;
$saving = 0;
$creditsTotal = 0;
$creditsUsedTotal = 0;


$rates = $IB -> request("getCurrencyExchangeRateList", [
    $IB->TOKEN,
    array('USD','EUR','RUB','UAH'),
    'central_bank',
    $IB -> SESS['TIME']*1000,
    new \Attributes()
]);
if ($IB->EXCEPTION) {
    $IB->EXCEPTION = '';
}
if ($rates) {
    foreach ($rates as $index => $rate) {
        $rates[$rate -> currency] = $rate -> buyRate;
        unset($rates[$index]);
    }
}

//=========================
$AllCard = $IB -> request("findAllCardContracts", [
    $IB->TOKEN,
    new \Attributes()
]);

if ($AllCard) {
    foreach ($AllCard as $accounts) {
        $spentCards += (
            isset($accounts -> balance) &&
            isset($accounts -> mainAccountCurrency) &&
            isset($accounts -> creditLimit) &&
            isset($accounts -> usedCreditLimit) &&
            isset($rates[$accounts -> mainAccountCurrency])
        ) ? (
            ($accounts -> creditLimit ? ($accounts -> creditLimit - $accounts -> usedCreditLimit) : $accounts -> balance) *
            $rates[$accounts -> mainAccountCurrency]
        ) : 0;

        $creditsTotal += (
            isset($accounts -> creditLimit) &&
            isset($accounts -> usedCreditLimit)
        ) ? (
            ($accounts -> creditLimit ? ($accounts -> creditLimit) : 0) *
            $rates[$accounts -> mainAccountCurrency]
        ) : 0;

        $creditsUsedTotal += (
            isset($accounts -> creditLimit) &&
            isset($accounts -> usedCreditLimit)
        ) ? (
            ($accounts -> creditLimit ? ($accounts -> usedCreditLimit) : 0) *
            $rates[$accounts -> mainAccountCurrency]
        ) : 0;
    }
}

//=========================
$AllAccount = $IB -> request("findAllAccountContracts", [
    $IB->TOKEN,
    new \Attributes()
]);

if ($AllAccount) {
    foreach ($AllAccount as $accounts) {
        $spentAccount += (
            isset($accounts -> balance) &&
            isset($accounts -> mainAccountCurrency) &&
            isset($rates[$accounts -> mainAccountCurrency])
        ) ? ($accounts -> balance * $rates[$accounts -> mainAccountCurrency]) : 0;
    }
}

//=========================
$AllCredit = $IB -> request("findAllCreditContracts", [
    $IB->TOKEN,
    new \Attributes()
]);

if ($AllCredit) {
    foreach ($AllCredit as $accounts) {
        $spentCredit += (
            isset($accounts -> balance) &&
            isset($accounts -> mainAccountCurrency) &&
            isset($rates[$accounts -> mainAccountCurrency])
        ) ? ($accounts -> balance * $rates[$accounts -> mainAccountCurrency]) : 0;
    }
    $creditsTotal += $spentCredit;
    $creditsUsedTotal += $spentCredit;
}

//=========================
$AllDeposit = $IB -> request("findAllDepositContracts", [
    $IB->TOKEN,
    new \Attributes()
]);

if ($AllDeposit) {
    foreach ($AllDeposit as $accounts) {
        $saving += (
            isset($accounts -> balance) &&
            isset($accounts -> mainAccountCurrency) &&
            isset($rates[$accounts -> mainAccountCurrency])
        ) ? ($accounts -> balance * $rates[$accounts -> mainAccountCurrency]) : 0;
    }
}

//=========================
$spentTotal = $spentCards + $spentAccount + $spentCredit;

switch ($type) {
    case "spent":
        ?>
        <div class="col-md-4 col-sm-6 col-xs-12 expand-container">
            <div class="overview expand-block">
                <div class="overview__inner">

                    <div class="overview__title">
                        <i class="icons">
                            <!--                    <img src="/dev/static/img/content/visa.svg">-->
                        </i>
                        <span class="title__name">{CABINET-CAPABILITIES-SPEND}</span>
                    </div>
                    <div class="overview__data expand" data-height="50" data-event="hover">
                        <div class="data">
                            <span class="overview__val"><?=$tpl->priceFormat($spentTotal)?></span>
                            <span class="overview__currency"> UAH</span>
                        </div>

                        <ul class="overview-prod__list">
                            <li class="overview-prod__item col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <span class="overview__title overview__title_det text-left">{CABINET-CAPABILITIES-ACCOUNTS}</span>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="overview__wrap overview__wrap_det">
                                        <span class="overview__val"><?=$tpl->priceFormat($spentAccount)?></span>
                                        <span class="overview__currency"> UAH</span>
                                    </div>
                                </div>
                            </li>
                            <li class="overview-prod__item  col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <span class="overview__title overview__title_det text-left">{CABINET-CAPABILITIES-CARDS}</span>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="overview__wrap overview__wrap_det">
                                        <span class="overview__val"><?=$tpl->priceFormat($spentCards)?></span>
                                        <span class="overview__currency"> UAH</span>
                                    </div>
                                </div>
                            </li>
                            <li class="overview-prod__item  col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <span class="overview__title overview__title_det text-left">{CABINET-CAPABILITIES-CREDITLIMIT}</span>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="overview__wrap overview__wrap_det">
                                        <span class="overview__val"><?=$tpl->priceFormat($spentCredit)?></span>
                                        <span class="overview__currency"> UAH</span>
                                    </div>
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
            <div class="overview expand-block">
                <div class="overview__inner">

                    <div class="overview__title">
                        <i class="icons">
                            <!--                    <img src="/dev/static/img/content/visa.svg">-->
                        </i>
                        <span class="title__name">{CABINET-CAPABILITIES-SAVING}</span>
                    </div>
                    <div class="overview__data expand" data-height="50" data-event="hover">
                        <div class="data">
                            <span class="overview__val"><?=$tpl->priceFormat($saving)?></span>
                            <span class="overview__currency"> UAH</span>
                        </div>

                        <ul class="overview-prod__list">
                            <li class="overview-prod__item col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <span class="overview__title overview__title_det"></span>
                                    <div class="overview__wrap overview__wrap_det">
                                        <span class="overview__val"><?=$tpl->priceFormat($saving / $rates['USD'])?></span>
                                        <span class="overview__currency"> USD</span>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <span class="overview__title overview__title_det"></span>
                                    <div class="overview__wrap overview__wrap_det">
                                        <span class="overview__val"><?=$tpl->priceFormat($saving / $rates['EUR'])?></span>
                                        <span class="overview__currency"> EUR</span>
                                    </div>
                                </div>
                            </li>
                            <!--                            <li class="overview-prod__item col-md-4">-->
                            <!--                                <span class="overview__title overview__title_det"></span>-->
                            <!--                                <div class="overview__wrap overview__wrap_det">-->
                            <!--                                    <span class="overview__val">--><?//=$tpl->priceFormat($saving / $rates['RUB'])?><!--</span>-->
                            <!--                                    <span class="overview__currency"> RUB</span>-->
                            <!--                                </div>-->
                            <!--                            </li>-->
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
            <div class="overview expand-block">
                <div class="overview__inner">

                    <div class="overview__title danger">
                        <i class="icons">
                            <!--                    <img src="/dev/static/img/content/visa.svg">-->
                        </i>
                        <span class="title__name">{CABINET-CAPABILITIES-OBLIGATIONS}</span>
                    </div>
                    <div class="overview__data expand" data-height="50" data-event="hover">
                        <div class="data">
                            <span class="overview__val"><?=$tpl->priceFormat($creditsUsedTotal)?></span>
                            <span class="overview__currency"> UAH</span>
                        </div>

                        <ul class="overview-prod__list">
                            <li class="overview-prod__item col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <span class="overview__title overview__title_det text-left">{CABINET-CAPABILITIES-CREDITS}</span>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="overview__wrap overview__wrap_det text-left">
                                        <span class="overview__val"><?=$tpl->priceFormat($creditsTotal)?></span>
                                        <span class="overview__currency"> UAH</span>
                                    </div>
                                </div>
                            </li>
                            <li class="overview-prod__item col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <span class="overview__title overview__title_det text-left">{CABINET-CAPABILITIES-CREDIT-USED}</span>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                                    <div class="overview__wrap overview__wrap_det text-left">
                                        <span class="overview__val"><?=$tpl->priceFormat($creditsUsedTotal)?></span>
                                        <span class="overview__currency"> UAH</span>
                                    </div>
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
?>


<script>
    (function ($) {
        var maxHeightExpand = 0;
        $(".expand").each(function () {
            var elem = $(this);
            var height = parseInt(elem.data("height"));
            var event = elem.data("event");
            var block = elem.closest(".expand-block");
            var container = elem.closest(".expand-container");
            var width_block = block.outerWidth();
            block.css({width:width_block});

            elem.css("height", height + "px");

            elem.hover(function () {
                block.addClass("selected");
            }, function () {
                block.removeClass("selected");
            });

            var container_height = container.height();
            if (container_height > maxHeightExpand) maxHeightExpand = container_height;
        });
        $(".expand-container").css("min-height", maxHeightExpand + "px");
    }(jQuery));

</script>
