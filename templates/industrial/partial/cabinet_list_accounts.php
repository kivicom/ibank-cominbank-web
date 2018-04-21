<?php
$ContractType = 2;
$getAllAccounts = $IB -> getAllAccounts([$ContractType]);
if ($getAllAccounts) {
    ?>

    <li class="li-grid-item">
        <div class="overview overview_accounts overview_accounts--mob js-slide_toggle">
            <div class="overview__inner hidden-xxs">
                <div class="services">
                    <div class="services__name js-slide_toggle__event">
                        <i class="icons icons_chevron"></i>
                        <span>{CABINET-LIST-ACCOUNTS-TITLE}</span>
                    </div>
                    <div class="btn-green">
                        <i class="icons icons_plus"></i>
                        <input class="btn-act" type="button" value="{CABINET-LIST-ACCOUNTS-OPEN}">
                    </div>
                </div>
                <div class="products products_accounts js-slide_toggle__block">
                    <table>
                        <?php
                        foreach ($getAllAccounts as $accounts) {
                            $cardNumb = isset($accounts['cards'] -> id) ? $accounts['cards'] -> id : "";
                            $balance = $accounts['balance'];

                            ?>
                            <tr class="products__item cards-title">
                                <td class="cards-title__td" onclick="location.href = '/<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_'.$IB->CONSTANTS['ContractType'][$ContractType].'S_URL'))?>?account=<?=urlencode($accounts['id'])?>&card=<?=urlencode($cardNumb)?>'">
                                    <div class="account-name"><?=$accounts['title']?></div>
                                </td>

                                <td class="money" onclick="location.href = '/<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_'.$IB->CONSTANTS['ContractType'][$ContractType].'S_URL'))?>?account=<?=urlencode($accounts['id'])?>&card=<?=urlencode($cardNumb)?>'">
                                    <span><?=$tpl->priceFormat($balance)?></span><span class="money_currency"><?=$accounts['currency']?></span>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                </div>
            </div>

            <div class="overview__inner hidden-lg hidden-md hidden-sm hidden-xs col-xxs">
                <div class="services">
                    <div class="services__name js-slide_toggle__event">
                        <i class="icons icons_chevron"></i>
                        <span>{CABINET-LIST-ACCOUNTS-TITLE}</span>
                    </div>
                    <div class="btn-green">
                        <i class="icons icons_plus"></i>
                        <input class="btn-act" type="button" value="{CABINET-LIST-ACCOUNTS-OPEN}">
                    </div>
                </div>

                <div class="products products_accounts js-slide_toggle__block">
                    <table>
                        <?php
                        foreach ($getAllAccounts as $accounts) {
                            $cardNumb = isset($accounts['cards'] -> id) ? $accounts['cards'] -> id : "";
                            $balance = $accounts['balance'];

                            ?>
                            <tr class="products__item cards-title">
                                <td class="cards-title__td" onclick="location.href = '/<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_'.$IB->CONSTANTS['ContractType'][$ContractType].'S_URL'))?>?account=<?=urlencode($accounts['id'])?>&card=<?=urlencode($cardNumb)?>'">
                                    <div class="account-name"><?=$accounts['title']?></div>
                                </td>
                            </tr class="products__item">
                                <td class="money" onclick="location.href = '/<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_'.$IB->CONSTANTS['ContractType'][$ContractType].'S_URL'))?>?account=<?=urlencode($accounts['id'])?>&card=<?=urlencode($cardNumb)?>'">
                                    <span><?=$tpl->priceFormat($balance)?></span><span class="money_currency"><?=$accounts['currency']?></span>
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


