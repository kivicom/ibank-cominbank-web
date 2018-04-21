<?php
$AllAccount = $IB -> request("findAllCardContracts", [
    $IB->TOKEN,
    new \Attributes()
]);


if ($AllAccount) {
    ?>

    <li class="li-grid-item">
        <div class="overview overview_accounts overview_accounts--mob js-slide_toggle">
            <div class="overview__inner hidden-xxs">
                <div class="services">
                    <div class="services__name js-slide_toggle__event">
                        <i class="icons icons_chevron"></i>
                        <span>{CABINET-LIST-ACCOUNTS-TITLE}</span>
                    </div>

<!--                    <input class="btn-green" type="button" value="{CABINET-LIST-ACCOUNTS-OPEN}">-->

                </div>
                <div class="products products_accounts js-slide_toggle__block">
                    <table>
                        <?php
                        foreach ($AllAccount as $accounts) {
                            $cardNumb = $accounts -> cards[0] -> id;

                            $productTitle = $accounts -> productTitle ? $accounts -> productTitle : $accounts -> legalNumber;

                            //$balance = $accounts -> creditLimit ? ($accounts -> creditLimit - $accounts -> usedCreditLimit) : $accounts->balance;
                            $balance = isset($accounts->balance) ? $accounts->balance : 0;
                            $balance += isset($accounts -> creditLimit) ? $accounts -> creditLimit : 0;
//                            $balance -= isset($accounts -> usedCreditLimit) ? $accounts -> usedCreditLimit : 0;

                            ?>
                            <tr class="products__item cards-title">
                                <td class="cards-title__td" onclick="location.href = '/<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_CARDS_URL'))?>?account=<?=urlencode($accounts -> id)?>&card=<?=$cardNumb?>'">
<!--                                    <input class="account-name" type="text" value="--><?//=$productTitle?><!--" disabled />-->
                                    <div class="account-name"><?=$productTitle?></div>
<!--                                    <i class="icons icons_write pen"></i>-->
                                </td>

                                <td class="money" onclick="location.href = '/<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_CARDS_URL'))?>?account=<?=urlencode($accounts -> id)?>&card=<?=$cardNumb?>'">
                                    <span><?=$tpl->priceFormat($balance)?></span><span class="money_currency"><?=$accounts->mainAccountCurrency?></span>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>
            </div>

        </div>


    </li>
    <?php
}

?>


