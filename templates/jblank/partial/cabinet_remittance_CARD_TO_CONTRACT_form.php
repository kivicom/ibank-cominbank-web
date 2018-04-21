<form class="cabinet_remittance_form_<?= $FinancialOperationTypeActive ?> validate"
      action="?FinancialOperationType=<?= $FinancialOperationTypeActive ?>" method="post">
    <input type="hidden" name="SERVICE" value="enroll" />

    <?php
    foreach ($data as $field => $parameter) {
        echo '<input type="hidden" name="data['.$field.']" value="'.urlencode($parameter).'" />';
    }
    $IB->includes("blocks/remittance_form_CARDNUM", [
        'FinancialOperation' => $FinancialOperation,
        'label' => '{CABINET-REMITTANCE-' . $FinancialOperation . '-FROM}',
        'data' => $data,
        'arrow' => TRUE
    ]);
    $IB->includes("blocks/remittance_form_card_date", ['FinancialOperation' => $FinancialOperation, 'data' => $data]);
    $IB->includes("blocks/remittance_form_card_secure_code", ['FinancialOperation' => $FinancialOperation]);
    $IB->includes("blocks/remittance_form_accounts_list", [
        'id' => 'CABINET-REMITTANCE-' . $FinancialOperation . '-TO',
        'data' => $data,
        'label' => '{CABINET-REMITTANCE-' . $FinancialOperation . '-TO}',
        'paremeters' => ['id' => 'id_to', 'type' => 'type_to', 'cardId' => 'card_to', 'currency' => 'currency_to'],
        'ContractType' => [],
        'disabled' => false
    ]);
    $IB->includes("blocks/remittance_form_amount", ['data' => $data]);
    $IB->includes("blocks/remittance_form_submit",['FinancialOperation' => $FinancialOperation]);
    ?>
</form>
