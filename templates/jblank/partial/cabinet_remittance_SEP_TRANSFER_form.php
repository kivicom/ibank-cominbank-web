<form class="cabinet_remittance_form_<?=$FinancialOperationTypeActive?> validate" action="?FinancialOperationType=<?=$FinancialOperationTypeActive?>" method="post">
    <input type="hidden" name="SERVICE" value="enroll" />

    <?php
    foreach ($data as $field => $parameter) {
        echo '<input type="hidden" name="data[' . $field . ']" value="' . htmlspecialchars($parameter) . '" />';
    }

    $getParameters = $data;
    if (isset($_GET['data'])) {
        if ($data['DIRECT'] === 'to') {
            $getParameters = [
                'type' => '',
                'id' => '',
                'cardId' => ''
            ];
        }
    }

    include "cabinet_remittance_".$FinancialOperation."_fields.php";

    $IB -> includes("blocks/remittance_form_submit",['FinancialOperation' => $FinancialOperation]);
    ?>
</form>