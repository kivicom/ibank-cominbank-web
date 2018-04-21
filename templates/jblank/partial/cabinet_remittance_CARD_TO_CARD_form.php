<form class="cabinet_remittance_form_<?=$FinancialOperationTypeActive?> validate" action="?FinancialOperationType=<?=$FinancialOperationTypeActive?>" method="post">
    <input type="hidden" name="SERVICE" value="enroll" />

    <?php
    foreach ($data as $field => $parameter) {
        echo '<input type="hidden" name="data['.$field.']" value="'.urlencode($parameter).'" />';
    }
    $IB->includes("blocks/remittance_form_accounts_list", [
        'id' => 'CABINET-REMITTANCE-' . $FinancialOperation . '-TO',
        'data' => isset($_GET['data']) ? (($data['DIRECT'] !== 'to') ?  $data : []) : $data,
        'label' => '{CABINET-REMITTANCE-' . $FinancialOperation . '-FROM}',
        'ContractType' => [4],
        'disabled' => false,
        'arrow' => TRUE
    ]);
    $IB -> includes("blocks/remittance_form_card_secure_code", ['FinancialOperation' => $FinancialOperation]);
    $IB -> includes("blocks/remittance_form_CARDNUM", [
            'FinancialOperation' => $FinancialOperation,
            'data' => $data,
            'label' => '{CABINET-REMITTANCE-'.$FinancialOperation.'-TO}',
            'parameter' => 'cardNumberTo'
    ]);
    $IB -> includes("blocks/remittance_form_amount", ['data' => $data]);
    $IB -> includes("blocks/remittance_form_submit",['FinancialOperation' => $FinancialOperation]);
    ?>
</form>
