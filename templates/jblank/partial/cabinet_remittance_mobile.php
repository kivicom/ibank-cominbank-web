<li  class="li-grid-item li-grid-item--mpay">
    <div class="overview overview_products">
        <div class="overview__inner">
            <div class="services">
                <div class="services__name">
                    <span>{CABINET-MOBILE-PAYMENT-TITLE}</span>
                </div>
            </div>
            <div class="products products_cards cf">
                <div class="mobile_payments">
                    <form action="/<?= $tpl->lang . $IB->CONFIG->get('CABINET_REMITTANCE_URL') ?>?FinancialOperationType=3" method="post" class="validate">
                        <input type="hidden" name="SERVICE" value="detection" />
                        <input type="hidden" name="data[detection_type]" value="mobile" />
                        <div class="form-group row">
                            <label class="col-lg-3 col-md-4 col-sm-5 col-xs-12">{CABINET-MOBILE-PAYMENT-NUMBER}</label>
                            <div class="col-lg-9 col-md-8 col-sm-7 col-xs-12">
                                <input type="text" name="data[phone_number]" class="form-control required" data-mask='phone'>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-md-4 col-sm-5 col-xs-12">{CABINET-MOBILE-PAYMENT-AMOUNT}</label>
                            <div class="col-lg-9 col-md-8 col-sm-7 col-xs-12">
                                <input type="text" name="data[amountInCents]" class="form-control required" data-mask='money'>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-lg-3 col-md-4 col-sm-5 col-xs-12">{CABINET-MOBILE-PAYMENT-ACCOUNT}</label>
                            <div class="col-lg-9 col-md-8 col-sm-7 col-xs-12">
                                <?php
                                $IB->includes("snippets/remittance_form_accounts_list", [
                                    'id' => 'MOBILE-PAYMENT-FORM',
                                    'ContractType' => [],
                                    'paremeters' => ['id', 'type', 'cardId' => 'card', 'currency']
                                ]);
                                ?>
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn-green">{CABINET-MOBILE-PAYMENT-BUTTON}</button>
                        </div>
                    </form>
                    <a href="/<?= ($tpl->lang . $IB->CONFIG->get('CABINET_REMITTANCE_URL')) ?>/?FinancialOperationType=3" class="sub-services__link sub-services__link_other sub-services__link_new">{CABINET-LIST-PAYMENTS-OTHERPAYMENTS}</a>

                </div>
            </div>
        </div>


    </div>
</li>