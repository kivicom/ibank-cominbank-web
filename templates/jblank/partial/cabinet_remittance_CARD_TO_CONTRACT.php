<?php
$FinancialOperation = strtr(basename(__FILE__, '.php'), array("cabinet_remittance_" => ""));

$CardToContract = false;

if (isset($_GET['secure3DRequest'])) {
    $data = isset($IB->SESS['data']) ? $IB->SESS['data'] : [];
    unset($IB->SESS['data']);
    $IB->SESSION();

    foreach ($data as $field => $value) $data[$field] = $value;
    $data['PaRes'] = isset($_POST['PaRes']) ? $_POST['PaRes'] : false;
    $data['MD'] = isset($_POST['MD']) ? $_POST['MD'] : false;
    ?>
    <style>* {display: none !important;} </style>
    <form id="secure3DRequest"
          action="/<?= ($tpl->lang . $IB->CONFIG->get('CABINET_REMITTANCE_URL')) ?>/?FinancialOperationType=<?= $FinancialOperationTypeActive ?>"
          method="post">
        <input type="hidden" name="SERVICE" value="commit"/>
        <?php
        foreach ($data as $field => $parameter) {
            echo '<input type="hidden" name="data[' . $field . ']" value="' . htmlspecialchars($parameter) . '" />';
        }
        ?>
    </form>
    <script>
        var form = document.getElementById("secure3DRequest");
        form.submit();
    </script>
    <?php
    exit();
}
//=================================================================================================

$SERVICE = isset($_POST['SERVICE']) ? $_POST['SERVICE'] : false;

if ($SERVICE !== false) {
    switch ($SERVICE) {
        case "enroll":
            $ContractReference = new \ContractReference();
            $ContractReference->id = $data['id_to'];
            $ContractReference->type = $data['type_to'];

            $CardReference = new \CardReference();
            $CardReference->cardNumber = preg_replace("/[^0-9]/", '', $data['cardNumber']);
            $CardReference->expMonth = preg_replace("/[^0-9]/", '', $data['expMonth']);
            $CardReference->expYear = preg_replace("/[^0-9]/", '', $data['expYear']);
            $CardReference->secureCode = preg_replace("/[^0-9]/", '', $data['secureCode']);
            $CardReference->cardNumberMask = null;

            $Attributes = new \Attributes();
            if (!empty($data['card_to'])) {
                $Attributes->attrs['destCardId'] = $data['card_to'];
            }

            $data['currency'] = isset($template->currency) ? $template->currency : "UAH";

            $CardToContract = $IB->request("enrollCardToContractTransfer", [
                $IB->TOKEN,
                $CardReference,
                $ContractReference,
                $amountInCents,
                $data['currency'],
                $Attributes
            ]);

            $SERVICE = $CardToContract ? (($CardToContract->status == 2) ? "commit" : "form") : "form";
            break;

        case "commit":
            $enrolledOperationId = ($data['enrolledOperationId'] !== '') ? $data['enrolledOperationId'] : false;
            $enrolledOperationId = $enrolledOperationId ? $enrolledOperationId : null;
            $secureCode = ($data['secureCode'] !== '') ? $data['secureCode'] : null;
            $MD = ($data['MD'] !== '') ? $data['MD'] : null;
            $PaRes = ($data['PaRes'] !== '') ? $data['PaRes'] : null;
            $OTP = ($data['otp'] !== '') ? $data['otp'] : null;
            if ($enrolledOperationId) {
                $CardToContract = $IB->request("commitCardToContractTransfer", [
                    $IB->TOKEN,
                    $enrolledOperationId,
                    $PaRes,
                    $MD,
                    $OTP,
                    $secureCode,
                    new \Attributes()
                ]);
            } else {
                $EXCEPTION = '{EXCEPTION-WRONG-CREDENTIALS}';
            }
            if ($CardToContract){
                switch ($CardToContract->status){
                    case 0:
                    case 3:
                        $SERVICE = "success";
                        break;
                    case 1:
                        $SERVICE = "form";
                        break;
                }
            }else{
                $SERVICE = "form";
            }
            break;
    }

    $EXCEPTION = $IB->EXCEPTION_process();

}

$financialOperationResult = $CardToContract;

?>

