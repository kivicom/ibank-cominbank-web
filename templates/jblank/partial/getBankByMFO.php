<?php
$MFO = isset($_GET['mfo']) ? ((string) $_GET['mfo']) : false;
$MFO = (strlen($MFO) == 6) ? $MFO : false;

if ($MFO !== false) {
    $result = $IB -> request(
        "getBankById",
        array(
            $MFO
        ));

    if ($result && isset($result -> bankName)) {
        $arr = array("request"=>$result -> bankName);
        echo json_encode($arr);
    }
}

exit;