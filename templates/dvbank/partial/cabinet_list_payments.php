<?php
if($IB->userRole('full')){

    $fetchBillerCategories = $IB->request("fetchBillerCategories", [
        $IB->TOKEN,
        null
    ]);

    $billersLink = $tpl->lang.$IB -> CONFIG -> get('CABINET_REMITTANCE_URL')."?FinancialOperationType=3";
?>

<li class="li-grid-item">
    <div class="overview overview_payments">
        <div class="overview__inner overview__inner_block">
            <div class="services">
                <div class="services__name js-slide_toggle__event">
                    <span>{CABINET-LIST-PAY-TRANSFER-TITLE}</span>
                </div>

            </div>

            <div class="sub-services sub-services--transfers">
                <ul class="sub-services--title clearfix">
                    <li class="sub-services--item col-lg-4 col-md-4 col-sm-4 col-xs-12"><a href="<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_REMITTANCE_URL')."?FinancialOperationType=1")?>" class="sub-services__link sub-services__link_green">{CABINET-LIST-PAYMENTS-INCOME}</a></li>
                    <li class="sub-services--item col-lg-4 col-md-4 col-sm-4 col-xs-12"><a href="<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_REMITTANCE_URL')."?FinancialOperationType=8")?>" class="sub-services__link sub-services__link_green">{CABINET-LIST-PAYMENTS-OUTCOME}</a></li>
                    <li class="sub-services--item col-lg-4 col-md-4 col-sm-4 col-xs-12"><a href="<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_REMITTANCE_URL')."?FinancialOperationType=2")?>" class="sub-services__link sub-services__link_green">{CABINET-LIST-PAYMENTS-REQUISITES}</a></li>
                </ul>
                <?php if(!empty($fetchBillerCategories)) { $i=0;?>
                <ul class="sub-services--inner clearfix">
                    <li class="list--item active">
                        <ul class="clearfix sub-services__li__ul">
                            <?php foreach ($fetchBillerCategories as $item) {
                                $billerLink = $billersLink . '&data[cat][0]='.$item->id;
                                include $tpl->pathFull . "/snippets/biller_category_icon.php";
                            ?>
                            <li class="col-lg-4 col-md-4 col-sm-4 col-xs-12">
                                <a href="<?=$billerLink?>" class="sub-services__link">
                                    <div class="sub-services__icon"><img src="<?=$filename?>" class="sub-services__icon__img"/></div>
                                    <span class="sub-services__name"><?=$item->title?></span>
                                </a>
                            </li>
                            <?php if (++$i >=3) break;} ?>
                        </ul>
                    </li>
                </ul>
                <div class="products products_cards cf">
                    <div class="mobile_payments clearfix">
                        <div class="services__name services__name--sub-title">
                            <span>{CABINET-LIST-MOB-PAY-TITLE}</span>
                        </div>
                        <form action="/<?= $tpl->lang . $IB->CONFIG->get('CABINET_REMITTANCE_URL') ?>?FinancialOperationType=3" method="post" class="validate clearfix">
                            <input type="hidden" name="SERVICE" value="detection" />
                            <input type="hidden" name="data[detection_type]" value="mobile" />
                            <div class="form-group col-lg-7 col-md-7 col-sm-7 col-xs-12">
                                <label class="">{CABINET-MOBILE-PAYMENT-NUMBER}</label>
                                <div class="">
                                    <input type="text" name="data[phone_number]" class="form-control required" data-mask='phone'>
                                </div>
                            </div>
                            <div class="form-group col-lg-5 col-md-75 col-sm-5 col-xs-12">
                                <label class="">{CABINET-MOBILE-PAYMENT-AMOUNT}</label>
                                <div class="">
                                    <input type="text" name="data[amountInCents]" class="form-control required" data-mask='money'>
                                </div>
                            </div>
                            <div class="text-center clearfix mpay__btn-wrap">
                                <button type="submit" class="btn-green btn-green_pay">{CABINET-MOBILE-PAYMENT-BUTTON}</button>
                            </div>
                        </form>
                        <a href="/<?= ($tpl->lang . $IB->CONFIG->get('CABINET_REMITTANCE_URL')) ?>/?FinancialOperationType=3" class="sub-services__link sub-services__link_other sub-services__link_new">{CABINET-LIST-PAYMENTS-OTHERPAYMENTS}</a>

                    </div>
                    </div>
                <?php } ?>
            </div>


    </div>
</li>
<?php } ?>
