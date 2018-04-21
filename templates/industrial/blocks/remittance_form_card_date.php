<div class="remittance_form_card_date form-group row">
    <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
    <label class="col-md-3 col-sm-3 col-xs-12 text-left">{CABINET-REMITTANCE-<?=$FinancialOperation?>-EXP}</label>
    <div class="col-md-7 col-sm-7 col-xs-12 text-left">
        <?php
        $IB->includes("snippets/remittance_form_card_date", [
            'id' => 'CABINET-REMITTANCE-' . $FinancialOperation . '-DATE',
            'selected' => $data
        ]);
        ?>
    </div>
    <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
</div>