<?php

//include($tpl -> pathFull."/php/mpdf60/mpdf.php");
if (isset($_POST['receipt_type'])) {
    $receipt_type = strtoupper($_POST['receipt_type']);
    $operationId = $_POST['operationId'];

    if (false !== ($index = array_search($receipt_type, $IB->CONSTANTS['ReportFormatType']))) {
        $ReportOptions = new \ReportOptions();
        $ReportOptions->type = $index;

        $receipt = $IB->request("fetchOperationReceipt", [
            $IB->TOKEN,
            $operationId,
            $ReportOptions,
            new \Attributes()
        ]);

        if ($IB->EXCEPTION) {
            $IB->EXCEPTION = '';
        }

        if (isset($receipt->body)) {
            switch ($receipt_type) {
                case "PDF":
                    header("Content-type:application/pdf");
                    echo base64_decode($receipt->body);
                    break;

                case "HTML":
                    header("Content-Type: text/html");
                    echo base64_decode($receipt->body);
                    break;
            }

        }
    }
}
exit();







//$_GET['FinancialOperationType'] = $_POST['FinancialOperationType'];
//$_POST['receipt_type'] = $_POST['receipt_type'];
//$_GET['operation'] = '';
//$_GET['receipt'] = '';
//
//include $_POST['TEMPLATE'].".php";
?>


