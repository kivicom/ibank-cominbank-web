<?php

$AllCredit = $IB -> request("findAllCreditContracts", [
    $IB->TOKEN,
    new \Attributes()
]);

if ($AllCredit) {
    ?>
    <li class="overview overview_credits js-slide_toggle">
        <div class="overview__inner">
            <div class="services">
                <div class="services__name js-slide_toggle__event">
                    <i class="icons icons_chevron"></i>
                    <span>{CABINET-LIST-CREDIT-TITLE}</span>
                </div>
                <div class="btn-green">
                    <i class="icons icons_plus"></i>
                    <input class="btn-act" type="button" value="Відкрити">
                </div>

            </div>
            <div class="products products_credits cf js-slide_toggle__block">
                <table>
                    <?php
                    foreach ($AllCredit as $credit) {
                        $productTitle = "Стандартний";
                        ?>
                        <tr class="products__item">
                            <td class="icon"><i class="icons"><img src="<?=$tpl -> pathTemplate?>/img/content/visa.svg"></i></td>
                            <td class="number"><span><?=$productTitle?></span></td>
                            <td class="money"><span><?=$tpl->priceFormat($credit->balance)?></span><span class="money_currency"><?=$credit->mainAccountCurrency?></span></td>
                            <td class="action">
                                <a href="#">
                                    <i class="icons"></i><span>Погасити</span>
                                </a>
                            </td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
                <a class="archive" href="#"><span>Архів</span></a>
            </div>
        </div>
    </li>
    <?php
}

?>


