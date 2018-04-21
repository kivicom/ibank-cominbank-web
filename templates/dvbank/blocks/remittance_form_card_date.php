<div class="remittance_form_card_date form-group col-lg-7 col-md-7 col-sm-12 col-xs-12">
    <label>{CABINET-REMITTANCE-<?=$FinancialOperation?>-EXP}</label>
    <div class="remittance_form_card_date__selects">
        <?php
        $IB->includes("snippets/remittance_form_card_date", [
            'id' => 'CABINET-REMITTANCE-' . $FinancialOperation . '-DATE',
            'selected' => $data
        ]);
        ?>
    </div>
</div>