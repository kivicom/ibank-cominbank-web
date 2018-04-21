<?php
$FinancialOperation = strtr(basename(__FILE__, '.php'), array("cabinet_remittance_" => ""));

$CardToCard = false;

if (isset($_GET['secure3DRequest'])) {
    $data = isset($IB->SESS['data']) ? $IB->SESS['data'] : [];
    unset($IB->SESS['data']);
    $IB->SESSION();

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

    $Attributes = new \Attributes();
    if (!empty($data['card'])) {
        $Attributes->attrs['cardId'] = $data['card'];
    }

    switch ($SERVICE) {
        case "enroll":
            $CardReference = new \CardReference();
            $ContractReference = new \ContractReference();
            $ContractReference->id = $data['id'];
            $ContractReference->type = 4;
            $CardReference->contractReference = $ContractReference;
            $CardReference->cardId = $data['card'];
            $CardReference->secureCode = preg_replace("/[^0-9]/", '', $data['secureCode']);

            $data['currency'] = isset($template->currency) ? $template->currency : "UAH";

            $CardReferenceTo = new \CardReference();
            $CardReferenceTo->cardNumber = preg_replace("/[^0-9]/", '', $data['cardNumberTo']);

            $CardToCard = $IB->request("enrollCardToCardTransfer", [
                $IB->TOKEN,
                $CardReference,
                $CardReferenceTo,
                $amountInCents,
                $data['currency'],
                $Attributes
            ]);

            $SERVICE = $CardToCard ? (($CardToCard->status == 2) ? "commit" : "form") : "form";

            break;

        case "commit":
            $enrolledOperationId = ($data['enrolledOperationId'] !== '') ? $data['enrolledOperationId'] : false;
            $enrolledOperationId = $enrolledOperationId ? $enrolledOperationId : null;
            $secureCode = ($data['secureCode'] !== '') ? $data['secureCode'] : null;
            $MD = ($data['MD'] !== '') ? $data['MD'] : null;
            $PaRes = ($data['PaRes'] !== '') ? $data['PaRes'] : null;
            $OTP = ($data['otp'] !== '') ? $data['otp'] : null;
            if ($enrolledOperationId) {
                $CardToCard = $IB->request("commitCardToCardTransfer", [
                    $IB->TOKEN,
                    $enrolledOperationId,
                    $PaRes,
                    $MD,
                    $OTP,
                    $secureCode,
                    $Attributes
                ]);
            } else {
                $EXCEPTION = '{EXCEPTION-WRONG-CREDENTIALS}';
            }

            if ($CardToCard){
                switch ($CardToCard->status){
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

$financialOperationResult = $CardToCard;

?>

