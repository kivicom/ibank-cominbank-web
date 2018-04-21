<?php

$AllDeposit = $IB -> request("findAllDepositContracts", [
    $IB->TOKEN,
    new \Attributes()
]);

if ($AllDeposit) {
    ?>
    <li class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
        <div class="overview overview_deposits js-slide_toggle">
            <div class="overview__inner">
                <div class="services">
                    <div class="stock stockt"></div>
                    <div class="services__name js-slide_toggle__event">
                        <i class="icons icons_chevron"></i>
                        <span>{CABINET-LIST-DEPOSIT-TITLE}</span>
                    </div>
                    <div class="btn-green">
                        <i class="icons icons_plus"></i>
                        <input class="btn-act" type="button" value="Відкрити">
                    </div>
                </div>
                <div class="products products_credits products_deposits js-slide_toggle__block">
                    <table>
                        <?php
                        foreach ($AllDeposit as $deposits) {
                            $productTitle = "Стандартний";
                            ?>
                            <tr class="products__item">
                                <td class="icon"><i class="icons"><img src="<?=$tpl -> pathTemplate?>/img/content/visa.jpg"></i></td>
                                <td class="number"><span><?=$productTitle?></span></td>
                                <td class="money"><span><?=isset($deposits->balance) ? $tpl->priceFormat($deposits->balance) : ""?></span><span class="money_currency"><?=$deposits->mainAccountCurrency?></span></td>
                                <td class="action">
                                    <a href="#">
                                        <i class="icons"></i><span>Поповнити</span>
                                    </a>
                                    <a href="#">
                                        <i class="icons"></i><span>Зняти</span>
                                    </a>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <a href="#" class="acts__link">
                        <span class="archive">Архів</span>
                    </a>
                </div>
            </div>
        </div>
    </li>
    <?php
}

?>


