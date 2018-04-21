<?php
$ContractType = isset($type) ? explode(",", $type) : [];
$show_product_title = $show_product_title ? (($show_product_title === "true") ? true : false) : false;
$allAccounts = $IB -> getAllAccounts($ContractType);
$title = isset($title) ? $title : "";
$button_title = isset($button_title) ? $button_title : "";
$button_url = isset($button_url) ? $button_url : "";
$button_url_class = isset($button_url_class) ? $button_url_class : "";

if($allAccounts) {
?>
<li  class="li-grid-item">
        <div class="overview overview_products js-slide_toggle">
            <div class="overview__inner">
                <div class="services">
                    <div class="services__name js-slide_toggle__event">
                        <i class="icons icons_chevron"></i>
                        <span><?=$title?></span>
                    </div>

                    <?php
                    if ($button_title) {
                        ?>
                        <a href="<?=$button_url ? $button_url : "#"?>" class="<?=$button_url_class?>">
                            <div class="btn-green">
                                <i class="icons icons_plus"></i>
                                <input class="btn-act" type="button" value="<?=$button_title?>">
                            </div>
                        </a>
                        <?php
                    }
                    ?>
                </div>
                <div class="products products_cards list-products cf js-slide_toggle__block">

                    <table class="operations-table-desktop">
                        <!--<thead>
                        <tr class="categories">
                            <th>{CABINET-LIST-PRODUCTS-TYPE}</th>
                            <th>{CABINET-LIST-PRODUCTS-BALANCE}</th>
                        </tr>
                        </thead>-->
                        <tbody>

                        <?php
                        $accountsList = array();
                        $i = 0;
                        foreach ($allAccounts as $accountItem){
                            $accountsList[$i]['mainAccountNumber'] = $accountItem['mainAccountNumber'];
                            $accountsList[$i]['title'] = '{CABINET-LIST-PRODUCTS-'.$IB->CONSTANTS['ContractType'][$accountItem['type']].'}';
                            $accountsList[$i]['type'] = $accountItem['type'];

                            $availableClass = $tpl -> priceColor($accountItem['balance']);
                            $accountsList[$i]['available'] = '<div class="'.$availableClass.'">'.$tpl -> priceFormat($accountItem['balance']).' {'.$accountItem['currency'].'}</div>';

                            switch ($accountItem['type']){
                                case 1:
                                    $totalDept = isset($accountItem['object'] -> totalDept) ? -$accountItem['object'] -> totalDept : 0;
                                    $creditClass = $tpl -> priceColor($totalDept);
                                    $accountsList[$i]['debt'] = $totalDept ? ('<div class="'.$creditClass.'">(' . $tpl -> priceFormat($totalDept) .' {'.$accountItem['currency'].'})</div>') : "";
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
                                        $accountsList[$i]['debt'] = $debt ? ('<div class="'.$creditClass.'">(' . $tpl -> priceFormat($debt) .' {'.$accountItem['currency'].'})</div>') : '';
                                    }
                                    $accountsList[$i]['link'] =  '/'.$tpl->lang.$IB -> CONFIG -> get('CABINET_CARDS_URL').'?account='.urlencode($accountItem['object'] -> id).'&card='.urlencode($accountItem['cardId']);
                                    break;
                            }
                            $i++;
                        }
                        foreach ($accountsList as $account) {   ?>
                        <tr class="operation" <?php if(isset($account['link'])) { ?> onclick="location.href = '<?=$account['link']?> '" <?php } ?>>
                            <td><?= $show_product_title ? $account['title'] : "" ?><div><?= ($account['type'] !== 4) ? $account['mainAccountNumber'] : "" ?></div></td>
                            <td>
                                <span class="status-available">{CABINET-LIST-PRODUCTS-AVAILIABLE}</span>
                                <?=$account['available'] ?>
                                <?php if (isset($account['debt'])) { ?>
                                    <?php
                                    echo $account['debt'];
                                } ?>
                            </td>
                        </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                    <table class="operations-table-mob">
                        <!--<thead>
                        <tr class="categories">
                            <th>{CABINET-LIST-PRODUCTS-ACCOUNTNUMBER}</th>
                            <th>{CABINET-LIST-PRODUCTS-BALANCE}</th>
                        </tr>
                        </thead>-->
                        <tbody>

                        <?php
                        $accountsList = array();
                        $i = 0;
                        foreach ($allAccounts as $accountItem){
                            $accountsList[$i]['mainAccountNumber'] = $accountItem['mainAccountNumber'];
                            $accountsList[$i]['title'] = '{CABINET-LIST-PRODUCTS-'.$IB->CONSTANTS['ContractType'][$accountItem['type']].'}';
                            $accountsList[$i]['type'] = $accountItem['type'];

                            if($accountItem['type'] == 4){
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
                            }elseif($accountItem['type'] == 1){
                                $totalDept = isset($accountItem['object'] -> totalDept) ? -$accountItem['object'] -> totalDept : 0;
                                $creditClass = $tpl -> priceColor($totalDept);
                                $accountsList[$i]['debt'] = '<div class="'.$creditClass.'">' . $tpl -> priceFormat($totalDept) .' {'.$accountItem['currency'].'}</div>';
                                $availableClass = $tpl -> priceColor($accountItem['balance']);
                                $accountsList[$i]['available'] = '<div class="'.$availableClass.'">'.$tpl -> priceFormat($accountItem['balance']).' {'.$accountItem['currency'].'}</div>';
                            }
                            else{
                                $availableClass = $tpl -> priceColor($accountItem['balance']);
                                $accountsList[$i]['available'] = '<div class="'.$availableClass.'">'.$tpl -> priceFormat($accountItem['balance']).' {'.$accountItem['currency'].'}</div>';
                            }
                            if($accountItem['type'] == 2){
                                $accountsList[$i]['link'] =  '/'.$tpl->lang.$IB -> CONFIG -> get('CABINET_ACCOUNTS_URL').'?account='.urlencode($accountItem['object'] -> id);
                            }

                            $i++;
                        }
                        foreach ($accountsList as $account) {   ?>
                        <tr class="operation" <?php if(isset($account['link'])) { ?> onclick="location.href = '<?=$account['link']?> '" <?php } ?>>
                            <td>

                                <?php if ($account['type'] !== 4):?>
                                <span class="acc__num"><?= $account['mainAccountNumber'] ?></span>
                                <?php endif; ?>
                                <span class="acc__name"><?= $show_product_title ? $account['title'] : "" ?></span>
                            </td>

                            <td align="right">
                                <span class="status-available">{CABINET-LIST-PRODUCTS-AVAILIABLE}</span>
                                <?=$account['available'] ?>
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


