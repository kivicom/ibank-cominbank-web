<?php
$allAccounts = $IB -> getAllAccounts();
if($allAccounts) {
?>
<li  class="li-grid-item">
        <div class="overview overview_products">
            <div class="overview__inner">
                <div class="services">
                    <div class="services__name">
<!--                        <i class="icons icons_chevron"></i>-->
                        <span>{CABINET-LIST-PRODUCTS-MYPRODUCTS}</span>
                    </div>
                </div>
                <div class="products products_cards list-products cf custom__scroll--js">

                    <table class="operations-table-desktop">
                        <thead>
                        <tr class="categories">
                            <th>{CABINET-LIST-PRODUCTS-ACCOUNTNUMBER}</th>
                            <th>{CABINET-LIST-PRODUCTS-TYPE}</th>
                            <th>{CABINET-LIST-PRODUCTS-BALANCE}</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $accountsList = array();
                        $i = 0;
                        foreach ($allAccounts as $accountItem){
                            $accountsList[$i]['mainAccountNumber'] = $accountItem['mainAccountNumber'];
                            $accountsList[$i]['title'] = '{CABINET-LIST-PRODUCTS-'.$IB->CONSTANTS['ContractType'][$accountItem['type']].'}';

                            $availableClass = $tpl -> priceColor($accountItem['balance']);
                            $accountsList[$i]['available'] = '<div class="'.$availableClass.'">'.$tpl -> priceFormat($accountItem['balance']).' {'.$accountItem['currency'].'}</div>';

                            switch ($accountItem['type']){
                                case 1:
                                    $totalDept = isset($accountItem['object'] -> totalDept) ? -$accountItem['object'] -> totalDept : 0;
                                    $creditClass = $tpl -> priceColor($totalDept);
                                    $accountsList[$i]['debt'] = '<div class="'.$creditClass.'">' . $tpl -> priceFormat($totalDept) .' {'.$accountItem['currency'].'}</div>';
                                    $availableClass = $tpl -> priceColor($accountItem['balance']);
                                    $accountsList[$i]['available'] = '<div class="'.$availableClass.'">'.$tpl -> priceFormat($accountItem['balance']).' {'.$accountItem['currency'].'}</div>';
                                    $accountsList[$i]['link'] =  '/'.$tpl->lang.$IB -> CONFIG -> get('CABINET_CREDITS_URL').'?account='.urlencode($accountItem['object'] -> id);
                                    break;
                                case 2:
                                    $accountsList[$i]['link'] =  '/'.$tpl->lang.$IB -> CONFIG -> get('CABINET_ACCOUNTS_URL').'?account='.urlencode($accountItem['object'] -> id);
                                    break;
                                case 3:
                                    $accountsList[$i]['link'] =  '/'.$tpl->lang.$IB -> CONFIG -> get('CABINET_DEPOSITS_URL').'?account='.urlencode($accountItem['object'] -> id);
                                    break;
                                case 4:
                                    $accountsList[$i]['title'] .= '<br/>'.$accountItem['title'];
                                    $creditLimit = isset($accountItem['object'] -> creditLimit) ? $accountItem['object'] -> creditLimit : 0;
                                    $available = $accountItem['available'];

                                    $availableClass = $tpl -> priceColor($available);
                                    $accountsList[$i]['available'] = '<div class="'.$availableClass.'">'.$tpl -> priceFormat($available).' {'.$accountItem['currency'].'}</div>';
                                    if(isset($accountItem['object'] -> usedCreditLimit)){
                                        $debt = -$accountItem['object'] -> usedCreditLimit;
                                        $creditClass = $tpl -> priceColor($debt);
                                        $accountsList[$i]['debt'] = '<div class="'.$creditClass.'">' . $tpl -> priceFormat($debt) .' {'.$accountItem['currency'].'}</div>';
                                    }
                                    $accountsList[$i]['link'] =  '/'.$tpl->lang.$IB -> CONFIG -> get('CABINET_CARDS_URL').'?account='.urlencode($accountItem['object'] -> id).'&card='.urlencode($accountItem['cardId']);
                                    break;
                            }
                            $i++;
                        }
                        foreach ($accountsList as $account) {   ?>
                        <tr class="operation product-list-item" <?php if(isset($account['link'])) { ?> onclick="location.href = '<?=$account['link']?> '" <?php } ?>>
                            <td  ><?= $account['mainAccountNumber'] ?></td>
                            <td><?= $account['title'] ?></td>
                            <td>
                                <span class="status-available">{CABINET-LIST-PRODUCTS-AVAILIABLE}</span>
                                <?=$account['available'] ?>

                                <?php if (isset($account['debt'])) { ?>
                                    <span class="status-text">{CABINET-LIST-PRODUCTS-DEBT}</span>
                                    <?php
                                    echo $account['debt'];
                                } ?>
                            </td>

                        </tr>
                        <?php } ?>

                        <tr class="more-button">
                            <td colspan="4" align="center">
                                <a class="moreproducts moreproducts--mob">
                                    <button class="btn-green">{CABINET-LIST-PRODUCTS-MOREPRODUCTS}</button>
                                </a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <table class="operations-table-mob">
                        <thead>
                        <tr class="categories">
                            <th>{CABINET-LIST-PRODUCTS-ACCOUNTNUMBER}</th>
                            <th>{CABINET-LIST-PRODUCTS-BALANCE}</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        foreach ($accountsList as $account) {   ?>
                        <tr class="operation" <?php if(isset($account['link'])) { ?> onclick="location.href = '<?=$account['link']?> '" <?php } ?>>
                            <td rowspan="2">
                                <span class="acc__num"><?= $account['mainAccountNumber'] ?></span>
                                <span class="acc__name"><?= $account['title'] ?></span>
                            </td>

                            <td align="right">
                                <span class="status-available">{CABINET-LIST-PRODUCTS-AVAILIABLE}</span>
                                <?=$account['available'] ?>

                            </td>
                        </tr>
                        <tr class="operation operation_row" <?php if(isset($account['link'])) { ?> onclick="location.href = '<?=$account['link']?> '" <?php } ?>>
                            <td align="right" colspan="2">


                                <?php if (isset($account['debt'])) { ?>
                                    <span class="status-text">{CABINET-LIST-PRODUCTS-DEBT}</span>
                                    <?php
                                    echo $account['debt'];
                                } ?>
                            </td>
                        </tr>
                        <?php } ?>

                        <tr class="more-button">
                            <td colspan="2" align="center">
                                <a class="moreproducts">
                                    <button class="btn-green">{CABINET-LIST-PRODUCTS-MOREPRODUCTS}</button>
                                </a>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </li>
    <script type="text/javascript">
        jQuery(document).ready(function () {
            (function ($) {
                $('.moreproducts').click(function () {
                    $('.list-products').toggleClass('open');
                });
            })(jQuery);
        });
    </script>
<?php } ?>


