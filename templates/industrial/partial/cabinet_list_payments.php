<li class="li-grid-item">
    <div class="overview overview_payments js-slide_toggle">
        <div class="overview__inner">
            <div class="services">
                <div class="services__name js-slide_toggle__event">
                    <i class="icons icons_chevron"></i>
                    <span>{CABINET-LIST-TRANSFERS-TITLE}</span>
                </div>
                <div class="btn-green">
                    <i class="icons icons_plus"></i>
                    <a href="<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_REMITTANCE_URL'))?>"><input class="btn-act" type="button" value="{CABINET-LIST-PAYMENTS-NEW}"></a>
                </div>
            </div>

            <div class="sub-services js-slide_toggle__block" style="background: url(<?=$tpl->path?>/images/payments.jpg); background-size: cover; overflow: hidden">
                <a href="<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_REMITTANCE_URL'))?>">
                    <div style="width:100%; height: 182px;"></div>
                </a>
            </div>
        </div>
    </div>
</li>


