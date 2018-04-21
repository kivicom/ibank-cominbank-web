<?php
$operationHistoryFilter = new \operationHistoryFilter();

$account = isset($_GET['account']) ? $_GET['account'] : "";
$cardId = isset($_GET['card']) ? $_GET['card'] : "";

$detail = $IB -> request("fetchCardContract", [
    $IB->TOKEN,
    $account,
    new \Attributes()
]);

$EXCEPTION = $IB -> EXCEPTION_process();

$AllCard = $IB -> request("findAllCardContracts", [
    $IB->TOKEN,
    new \Attributes()
]);

$EXCEPTION = $IB -> EXCEPTION_process();

if ($detail && $AllCard) {

    $listCards = array();
    foreach ($AllCard as $CardItem) {
        foreach ($CardItem -> cards as $cards) {
            if (isset($CardItem -> id) && isset($cards -> id)) {
                $listCards[] = $CardItem -> id.",".$cards -> id;
            }
        }
    }

    if (isset($detail -> cards) && is_array($detail -> cards)) {
        foreach ($detail -> cards as $card) {
            if (isset($card -> id) && ($cardId == $card -> id)) {


                $getAuthSession = $IB -> request("getAuthSession", [
                    $IB->TOKEN
                ]);

                $cardStatus = $card->status;
                $cardAction = false;

                // блокировка карты
                if (isset($_GET['account']) && isset($_GET['card']) && isset($_GET['action'])){
                    switch ($_GET['action']){
                        case 'lock':
                            $actionMethod = "lockCard";
                            break;
                        case 'unlock':
                            $actionMethod = "unlockCard";
                            break;
                    }
                    $cardAction = $IB -> request($actionMethod, [
                        $IB->TOKEN,
                        $account,
                        $card->cardNumberMask,
                        new \Attributes()
                    ]);



                    if ($IB -> EXCEPTION){
                        if (isset($IB -> EXCEPTION -> errorMessageKey)) {
                            $trans = "{EXCEPTION-".strtoupper(strtr($IB -> EXCEPTION -> errorMessageKey, array("_"=>"-")))."}";
                            if (isset($I18N[$trans])) {
                                $EXCEPTION = $I18N[$trans];
                                $IB -> EXCEPTION = '';
                            }
                        }
                    } else {
                        $IB->SESS['MODAL'] = array(
                            "title"=>"{CABINET-CARD-STATUS-LOCK-CHANGE}",
                            "content" => "{CABINET-CARD-STATUS-CHANGE-STATUS-".strtoupper($actionMethod)."-".$cardAction->status."}"
                        );
                        $tpl -> SESSION();

                        header('Location: /'.$tpl->lang.$IB -> CONFIG -> get('CABINET_CARDS_URL')."?account=".(urlencode($account)."&card=".(urlencode($cardId))));
                        exit();
                    }


                    // на основе статуса операции (0 - success )определяем статус карты
                    if (empty($cardAction->status)){
                        switch ($_GET['action']){
                            case 'lock':
                                $cardStatus = 0;
                                break;
                            case 'unlock':
                                $cardStatus = 1;
                                break;
                        }
                    }
                }

                $cardNumberMask = $card -> cardNumberMask;
                $MasterVisa = substr($cardNumberMask, 0, 1);
                switch ($MasterVisa){
                    case 4:
                        $MasterVisaTitle = '{CABINET-CARD-DETAIL-VISACARD}';
                        break;
                    case 5:
                        $MasterVisaTitle = '{CABINET-CARD-DETAIL-MASTERCARD}';
                        break;
                }

                //$productTitle = isset($detail -> productTitle) ? ($detail -> productTitle ? $detail -> productTitle : "{CABINET-CARD-DETAIL-VISACARD}" ) : "{CABINET-CARD-DETAIL-VISACARD}";
                $productTitle = $MasterVisaTitle;
                $creditLimit = isset($detail -> creditLimit) ? $detail -> creditLimit : 0;
                $usedCreditLimit = isset($detail -> usedCreditLimit) ? $detail -> usedCreditLimit : 0;

                //$available = $creditLimit - $usedCreditLimit;
                $available = $creditLimit;
                $available = isset($detail -> balance) ? ($available + $detail -> balance) : $available;

                $creditLimit = $creditLimit ? $tpl->priceFormat($creditLimit) : 0;
                $usedCreditLimit = $usedCreditLimit ? $tpl -> priceFormat($usedCreditLimit) : 0;
                $available = $available ? $tpl -> priceFormat($available) : 0;

                $mainAccountCurrency = isset($detail -> mainAccountCurrency) ? $detail -> mainAccountCurrency : "";
                $balance = isset($detail -> balance) ? $tpl->priceFormat($detail -> balance) : 0;

                $mainAccountNumber = isset($detail -> mainAccountNumber) ? $detail -> mainAccountNumber : "";

                $leftCardUrl = $detail -> id.",".$card ->id;
                if (false !== ($pos = array_search($leftCardUrl, $listCards))) {
                    if (isset($listCards[$pos - 1])) {
                        $leftCardUrl = explode(",",$listCards[$pos - 1]);
                        $leftCardUrl = $tpl->lang.$IB -> CONFIG -> get('CABINET_CARDS_URL')."?account=".(urlencode($leftCardUrl[0]))."&card=".(urlencode($leftCardUrl[1]));
                    } else {
                        $leftCardUrl = false;
                    }
                }

                $rightCardUrl = $detail -> id.",".$card ->id;
                if (false !== ($pos = array_search($rightCardUrl, $listCards))) {
                    if (isset($listCards[$pos + 1])) {
                        $rightCardUrl = explode(",",$listCards[$pos + 1]);
                        $rightCardUrl = $tpl->lang.$IB -> CONFIG -> get('CABINET_CARDS_URL')."?account=".(urlencode($rightCardUrl[0]))."&card=".(urlencode($rightCardUrl[1]));
                    } else {
                        $rightCardUrl = false;
                    }
                }

                $cardNumberMask = $card -> cardNumberMask;
                $MasterVisa = substr($cardNumberMask, 0, 1);


                if (isset($card->attributes->attrs['skin'])){
                    $cardSkin = ($IB -> CONFIG -> get('CARDS_SKIN_DIR')).$card->attributes->attrs['skin'].'.png';
                }
                else{
                    switch ($MasterVisa) {
                        case "4":
                            $cardSkin = $tpl->path.'/images/cards/visacard.png';
                            break;
                        case "5":
                            $cardSkin = $tpl->path.'/images/cards/mastercard.png';
                            break;
                        case "6":
                            $cardSkin = $tpl->path.'/images/cards/mastercard.png';
                            break;
                    }
                }

                ?>
                <div class="products-card col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <table class="products-card__detail hidden-xs">
                        <tr>
                            <td rowspan="2" width="50%" class="products-card__detail_left">
                                <div class="overview">
                                    <div class="overview__inner">
                                        <div class="overview__title">
<!--                                            <i class="icons">-->
<!--                                                <img src="/dev/static/img/content/visa.svg">-->
<!--                                            </i>-->
                                            <span class="title__name">
                                                <?=$productTitle?>
                                            </span>
                                        </div>
                                        <div class="overview__data products-card__imageblock">
                                            <table>
                                                <tr>
                                                    <td class="col-lg-1 col-md-1 col-sm-1 products-card__left" align="center">
                                                        <?php
                                                        if ($leftCardUrl) {
                                                            ?>
                                                            <a href="<?=$leftCardUrl?>">
                                                                <img src="<?=$tpl->path?>/images/icons/left-arrow-min.png" />
                                                            </a>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <img class="hidden-visible" src="<?=$tpl->path?>/images/icons/left-arrow-min.png" />
                                                            <?php
                                                        }
                                                        ?>
                                                    </td>
                                                    <td class="col-lg-10 col-md-10 col-sm-10">
                                                        <img class="products-card__image" src="<?=$cardSkin?>" />
                                                        <div class="products-card__imagebg"><?=($available." ".$mainAccountCurrency)?></div>
                                                    </td>
                                                    <td class="col-lg-1 col-md-1 col-sm-1 products-card__right" align="center">
                                                        <?php
                                                        if ($rightCardUrl) {
                                                            ?>
                                                            <a href="<?=$rightCardUrl?>">
                                                                <img src="<?=$tpl->path?>/images/icons/right-arrow-min.png" />
                                                            </a>
                                                            <?php
                                                        } else {
                                                            ?>
                                                            <img class="hidden-visible" src="<?=$tpl->path?>/images/icons/right-arrow-min.png" />
                                                            <?php
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                            </table>
                                            <div class="col-lg-1 col-md-1 col-sm-1"></div>
                                            <div class="col-lg-10 col-md-10 col-sm-10 products-card__operations text-center">
                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                   <a href="<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_REMITTANCE_URL')."?FinancialOperationType=1&data[id]=".(urlencode($account))."&data[type]=4&data[cardId]=".(urlencode($cardId))."&data[DIRECT]=to")?>" data-toggle="tooltip" data-placement="bottom" title="Поповнити">
                                                        <img src="<?=$tpl->path?>/images/icons/share1-min.png" />
                                                    </a>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                    <a href="<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_REMITTANCE_URL')."?FinancialOperationType=1&data[id]=".(urlencode($account))."&data[type]=4&data[cardId]=".(urlencode($cardId)))?>" data-toggle="tooltip" data-placement="bottom" title="Переказати">
                                                        <img src="<?=$tpl->path?>/images/icons/noun-min.png" />
                                                    </a>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                    <a href="<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_REMITTANCE_URL')."?FinancialOperationType=2&data[id]=".(urlencode($account))."&data[type]=4&data[cardId]=".(urlencode($cardId)))?>" data-toggle="tooltip" data-placement="bottom" title="Сплатити">
                                                        <img src="<?=$tpl->path?>/images/icons/share2-min.png" />
                                                    </a>
                                                </div>
                                            </div>
                                            <div class="col-lg-1 col-md-1 col-sm-1"></div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td  class="products-card__detail_top">
                                <div class="overview">
                                    <div class="overview__inner">
                                        <div class="services">
                                            <div class="services__name">
                                                <span><?=$productTitle?></span>
                                            </div>
                                        </div>
                                        <div class="overview__data">
                                            <?=isset($card -> cardNumberMask) ? $card -> cardNumberMask : ""?>
                                        </div>
                                        <div class="overview__footer">
                                            <div>{CABINET-CARD-DETAIL-VALIDITY}: <span><?=isset($card->expiryMonth) ? $card->expiryMonth : ""?>/<?=isset($card->expiryMonth) ? substr($card->expiryYear, -2) : ""?></span></div>
<!--                                            <div>Код CVV: <span>___</span></div>-->
                                            <div>
                                                <form method="get" action="/<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_CARDS_LIMIT'))?>">
                                                    <input type="hidden" name="account" value="<?=$account?>" />
                                                    <input type="hidden" name="card" value="<?=$cardId?>" />
                                                    <button class="tcb">{CABINET-CARD-DETAIL-LIMIT}</button>
                                                </form>
                                            </div>
                                        </div>

                                        <div class="overview__footer">
                                            <div>{CABINET-CARD-STATUS}: <span><?=($cardStatus ? "{CABINET-CARD-STATUS-LOCK}" : "{CABINET-CARD-STATUS-UNLOCK}")?></span></div>
                                        </div>

                                        <div class="overview__footer">
                                            <div>
                                            <form method="get" action="" class="validate">
                                                <input type="hidden" name="account" value="<?=$account?>" />
                                                <input type="hidden" name="card" value="<?=$cardId?>" />
                                                <input type="hidden" name="action" value="<?=($cardStatus)? 'lock': 'unlock'?>" />
                                                <?php
                                                $getAuthSession = $IB -> request("getAuthSession", [
                                                    $IB->TOKEN
                                                ]);
                                                echo ($getAuthSession->level)? '' : '<input class="system_ext_auth_required required" type="hidden" value="" />'
                                                ?>
                                                <button class="tcb"><?=($cardStatus)? "{CABINET-CARD-DETAIL-BLOCK-CARD}": "{CABINET-CARD-DETAIL-UNBLOCK-CARD}"?></button>
                                            </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <div class="overview">
                                    <div class="overview__inner">
                                        <div class="overview__data products-card__listdetail">
                                            <ul>
                                                <li>
                                                    <div class="col-lg-6 col-md-6 col-sm-6">{CABINET-CARD-DETAIL-CREDITLIMIT}</div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6 text-right">
                                                        <?=$creditLimit?> <?=$mainAccountCurrency?>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="col-lg-6 col-md-6 col-sm-6">{CABINET-CARD-DETAIL-ARREARS}</div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6 text-right">
                                                        <?=$usedCreditLimit?> <?=$mainAccountCurrency?>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="col-lg-6 col-md-6 col-sm-6">{CABINET-CARD-DETAIL-AVAIL}</div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6 text-right">
                                                        <?=$available?> <?=$mainAccountCurrency?>
                                                    </div>
                                                </li>
                                                <li>
                                                    <div class="col-lg-6 col-md-6 col-sm-6">{CABINET-CARD-DETAIL-ACCOUNT}</div>
                                                    <div class="col-lg-6 col-md-6 col-sm-6 text-right">
                                                        <?=$mainAccountNumber?>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>


                    <div class="products-card__detail hidden-lg hidden-md hidden-sm">
                        <div class="products-card__detail_left">
                            <div class="overview">
                                <div class="overview__inner">
                                    <div class="overview__title">
<!--                                        <i class="icons">-->
<!--                                            <img src="/dev/static/img/content/visa.svg">-->
<!--                                        </i>-->
                                        <span class="title__name">
                                            <?=isset($detail -> productTitle) ? ($detail -> productTitle ? $detail -> productTitle : "Карта Visa" ) : ""?>
                                        </span>
                                    </div>
                                    <div class="overview__data products-card__imageblock">
                                        <table>
                                            <tr>
                                                <td class="controls controls_left" align="center">
                                                    <a href="#">
                                                        <img src="<?=$tpl->path?>/images/icons/left-arrow-min.png" />
                                                    </a>
                                                </td>
                                                <td class="">
                                                    <img class="products-card__image" src="<?=$cardSkin?>" />
                                                    <div class="products-card__imagebg">
                                                        <?=($available." ".$mainAccountCurrency)?>
                                                    </div>
                                                </td>
                                                <td class="controls controls_right" align="center">
                                                    <a href="#">
                                                        <img src="<?=$tpl->path?>/images/icons/right-arrow-min.png" />
                                                    </a>
                                                </td>
                                            </tr>
                                        </table>
                                        <div class=""></div>
                                        <div class="products-card__operations text-center">
                                            <div class="col-xs-4">
                                                <a href="#" data-toggle="tooltip" data-placement="bottom" title="Поповнити">
                                                    <img src="<?=$tpl->path?>/images/icons/share1-min.png" />
                                                </a>
                                            </div>
                                            <div class="col-xs-4">
                                                <a href="<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_REMITTANCE_URL')."?own&account=$account&type=4")?>" data-toggle="tooltip" data-placement="bottom" title="Переказати">
                                                    <img src="<?=$tpl->path?>/images/icons/noun-min.png" />
                                                </a>
                                            </div>
                                            <div class="col-xs-4">
                                                <a href="<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_REMITTANCE_URL')."?requisites&account=$account&type=4")?>" data-toggle="tooltip" data-placement="bottom" title="Сплатити">
                                                    <img src="<?=$tpl->path?>/images/icons/share2-min.png" />
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-xs-1"></div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div  class="products-card__detail_top">
                            <div class="overview">
                                <div class="overview__inner">
                                    <div class="services">
                                        <div class="services__name">
                                            <span>Перекази</span>
                                        </div>
                                    </div>
                                    <div class="overview__data">
                                        <?=isset($card -> cardNumberMask) ? $card -> cardNumberMask : ""?>
                                    </div>
                                    <div class="overview__footer">
                                        <div>Термін дії: <span><?=isset($card->expiryMonth) ? $card->expiryMonth : ""?>/<?=isset($card->expiryMonth) ? substr($card->expiryYear, -2) : ""?></span></div>
<!--                                        <div>Код CVV: <span>___</span></div>-->
                                        <div>
                                            <form method="get" action="/<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_CARDS_LIMIT'))?>">
                                                <input type="hidden" name="account" value="<?=$account?>" />
                                                <input type="hidden" name="card" value="<?=$cardId?>" />
                                                <button class="tcb">{CABINET-CARD-DETAIL-LIMIT}</button>
                                            </form>
                                        </div>
                                    </div>
                                    <div class="overview__footer">
                                        <div>
                                            <form method="get" action="" class="validate">
                                                <input type="hidden" name="account" value="<?=$account?>" />
                                                <input type="hidden" name="card" value="<?=$cardId?>" />
                                                <input type="hidden" name="action" value="<?=($cardStatus)? 'lock': 'unlock'?>" />
                                                <?php
                                                $getAuthSession = $IB -> request("getAuthSession", [
                                                    $IB->TOKEN
                                                ]);
                                                echo ($getAuthSession->level)? '' : '<input class="system_ext_auth_required required" type="hidden" value="" />'
                                                ?>
                                                <button class="tcb"><?=($cardStatus)? "{CABINET-CARD-DETAIL-BLOCK-CARD}": "{CABINET-CARD-DETAIL-UNBLOCK-CARD}"?></button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div>
                            <div class="overview">
                                <div class="overview__inner">
                                    <div class="overview__data products-card__listdetail">
                                        <ul>
                                            <li>
                                                <div class="col-xs-6">Кредитний ліміт</div>
                                                <div class="col-xs-6 text-right">
                                                    <?=$creditLimit?> <?=$mainAccountCurrency?>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="col-xs-6">Заборгованність</div>
                                                <div class="col-xs-6 text-right">
                                                    <?=$usedCreditLimit?> <?=$mainAccountCurrency?>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="col-xs-6">Доступно</div>
                                                <div class="col-xs-6 text-right">
                                                    <?=$available?> <?=$mainAccountCurrency?>
                                                </div>
                                            </li>
                                            <li>
                                                <div class="col-xs-6">Рахунок</div>
                                                <div class="col-xs-6 text-right">
                                                    <?=$mainAccountNumber?>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                [cabinet_operations account="<?=$account?>" type="4" tabs="finance"][/cabinet_operations]

                <?php
                if (!empty($IB->SESS['MODAL'])) {
                    ?>
                    <div id="cabinet_card_detail_status" class="modal fade" role="dialog">
                        <div class="modal-dialog">
                            <!-- Modal content-->
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    <h4 class="modal-title"><?=$IB->SESS['MODAL']['title']?></h4>
                                </div>
                                <div class="modal-body preloader_block preloader_complete">
                                    <?=$IB->SESS['MODAL']['content']?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <script>
                        jQuery("#cabinet_card_detail_status").modal();
                    </script>
                    <?php
                    $IB->SESS['MODAL'] = false;
                    $tpl -> SESSION();
                }

                break;
            }
        }
    }



}


?>



