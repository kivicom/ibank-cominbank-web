<?php
$AllCard = $IB -> request("findAllCardContracts", [
    $IB->TOKEN,
    new \Attributes()
]);

if ($AllCard) {
    ?>

   <li  class="li-grid-item">
        <div class="overview overview_products overview_products--mob js-slide_toggle">
            <div class="overview__inner hidden-xxs">
                <div class="services">
                    <div class="services__name js-slide_toggle__event">
                        <i class="icons icons_chevron"></i>
                        <span>{CABINET-LIST-CARDS-TITLE}</span>
                    </div>

                    <input class="btn-green" type="button" value="{CABINET-LIST-CARDS-ORDER}">

                </div>
                <div class="products products_cards cf js-slide_toggle__block">
                    <table class="cards-table">
                        <?php
                        foreach ($AllCard as $accounts) {
                            if (isset($accounts -> cards)) {
                                foreach ($accounts -> cards as $cards) {
                                    $productTitle = isset($accounts -> productTitle) ? $accounts -> productTitle : "Карта Visa";
                                    $cardURL = '/'.$tpl->lang.$IB -> CONFIG -> get('CABINET_CARDS_URL')."?account=".$accounts->id."&card=".$cards->id;
                                    $cardNumberMask = $cards -> cardNumberMask;
                                    $MasterVisa = substr($cardNumberMask, 0, 1);
                                    //$balance = $accounts -> creditLimit ? ($accounts -> creditLimit - $accounts -> usedCreditLimit) : $accounts->balance;
                                    $balance = isset($accounts->balance) ? $accounts->balance : 0;
                                    $balance += isset($accounts -> creditLimit) ? $accounts -> creditLimit : 0;
//                                    $balance -= isset($accounts -> usedCreditLimit) ? $accounts -> usedCreditLimit : 0;
                                    ?>
                                    <tr class="products__item">
                                        <td class="icon" onclick="window.location.href='<?=$cardURL?>'; return false">
                                            <i class="icons">
                                                <?php
                                                switch ($MasterVisa) {
                                                    case "4":
                                                        ?>
                                                        <img src="<?=$tpl->path?>/images/cards/visa.svg">
                                                        <?php
                                                        break;
                                                    case "5":
                                                        ?>
                                                            <img src="<?=$tpl->path?>/images/cards/mc.svg">
                                                        <?php
                                                        break;
                                                }
                                                ?>
                                            </i>
                                        </td>
                                        <td class="number" onclick="window.location.href='<?=$cardURL?>'; return false">
                                            <span>**&nbsp<?=substr($cards->cardNumberMask,-4)?></span>
                                        </td>
                                        <td class="name" onclick="window.location.href='<?=$cardURL?>'; return false">    <span><?=$productTitle?></span>
                                        </td>
                                        <td class="money" onclick="window.location.href='<?=$cardURL?>'; return false">
                                            <span><?=$tpl->priceFormat($balance)?></span><span class="money_currency"><?=$accounts->mainAccountCurrency?></span>
                                                <!-- <i class="icons icons_settings"></i> -->
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                        }
                        ?>
                    </table>
<!--                    <a class="archive" href="#"><span>Архів</span></a>-->
                </div>
            </div>
        </div>
    </li>






    <?php
}

?>


