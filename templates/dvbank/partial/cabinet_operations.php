<?php
$account = isset($account) ? $account : false;
$type = isset($type) ? $type : false;
$tabs = isset($tabs) ? explode(",", $tabs) : false;
$tab = (isset($_GET['tab'])) ? ((int) $_GET['tab']) : 1;

$data = isset($_GET['data']) ? $_GET['data']: array();

$historyFinanceData = $IB -> getFinanceOperations($account, $type, $data);
$operationHistoryFilter = new \operationHistoryFilter();
?>
<ul class="operations__list col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <?php
    if (($tabs === false) or (is_array($tabs) && in_array("finance", $tabs))) {
        ?>
        <li class="<?=(($tab==1) ? "active" : "")?>" data-tab_for="1"><a href="" ><span>{CABINET-OPETATION-FINANCE}</span></a></li>
        <?php
    }
    if (($tabs === false) or (is_array($tabs) && in_array("service", $tabs))) {
        ?>
        <li class="<?=(($tab==2) ? "active" : "")?>" data-tab_for="2"><a href=""><span>{CABINET-OPETATION-SERVICE}</span></a></li>
        <?php
    }
    if (($tabs === false) or (is_array($tabs) && in_array("auth", $tabs))) {
        ?>
        <li class="<?=(($tab==3) ? "active" : "")?>" data-tab_for="3"><a href=""><span>{CABINET-OPETATION-AUTH}</span></a></li>
        <?php
    }
    ?>
</ul>

<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
    <?php
    $IB -> includes("blocks/cabinet_operations_filter", ['historyFinanceData' => $historyFinanceData]);
    ?>

    <div class="overview operations-table <?=(($tab==1) ? "active" : "")?> " data-tab_for="1">
        <div class="tabs active-table">
            <?php
            $IB -> includes("blocks/finance_operations", ['historyFinanceData' => $historyFinanceData]);
            ?>
        </div>
    </div>
<?php
if (!$account) {
    $fetchCommonOperations = $IB->request("fetchCommonOperations", [
        $IB->TOKEN,
        $operationHistoryFilter,
        ($historyFinanceData['modeFilter'] == "last") ? ($historyFinanceData['currentDateUnix'] - ($historyFinanceData['lastFilter'] * 3600 * 24)) * 1000 : ($historyFinanceData['dateFromUnix'] * 1000),
        ($historyFinanceData['modeFilter'] == "last") ? ($historyFinanceData['currentDateUnix'] + 3600 * 24) * 1000 : ($historyFinanceData['dateToUnix'] * 1000),
        new \Attributes()
    ]);

    if ($fetchCommonOperations) {
        ?>
        <div class="overview operations-table <?= (($tab == 2) ? "active" : "") ?> " data-tab_for="2">
            <div class="tabs">
                <table class="operations-table-desktop">
                    <thead>
                    <tr class="categories">
                        <th class="date">{CABINET-OPETATION-TABLE-DATE}</th>
                        <th>{CARD}</th>
                        <th>{CABINET-OPETATION-TABLE-OPERATION}</th>
                        <th>{USER-OPERATION-STATUS}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($fetchCommonOperations as $fetchCommonOperation) {
                        $historyDateTime = isset($fetchCommonOperation->executeDate) ? $fetchCommonOperation->executeDate / 1000 : "";
                        $historyDate = date("d.m.Y", $historyDateTime);
                        $historyTime = date("H:i", $historyDateTime);
                        ?>
                        <tr class="operation">
                            <td class="date"><span><?= $historyDate ?></span><span><?= $historyTime ?></span></td>
                            <td>
                                <span><?= (isset($fetchCommonOperation->dstCardNumberMask) ? $fetchCommonOperation->dstCardNumberMask : "") ?></span>
                            </td>
                            <td>
                                <span><?= (isset($fetchCommonOperation->description) ? ("{COMMON-OPERATION-TYPE-".$fetchCommonOperation->type."}") : "") ?></span>
                            </td>
                            <td class="user-operation-status-<?= $fetchCommonOperation->status ?>"><span>{USER-OPERATION-STATUS-<?= $fetchCommonOperation->status ?>}</span></td>
                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>

                <ul class="operations-table-mobile">
                    <?php
                    foreach ($fetchCommonOperations as $fetchCommonOperation) {
                        $historyDateTime = isset($fetchCommonOperation->executeDate) ? $fetchCommonOperation->executeDate / 1000 : "";
                        $historyDate = date("d.m.Y", $historyDateTime);
                        $historyTime = date("H:i", $historyDateTime);
                        ?>
                        <li class="col-xs-12">
                            <div class="col-xs-8">
                                <div class="money">
                                    <?= (isset($fetchCommonOperation->dstCardNumberMask) ? $fetchCommonOperation->dstCardNumberMask : "") ?>
                                </div>
                                <div><span><?= (isset($fetchCommonOperation->description) ? ("{COMMON-OPERATION-TYPE-".$fetchCommonOperation->type."}") : "") ?></span></div>
                            </div>
                            <div class="col-xs-4">
                                <span><?= $historyDate ?></span><span><?= $historyTime ?></span>
                                <span class="user-operation-status-<?= $fetchCommonOperation->status ?>"><span>{USER-OPERATION-STATUS-<?= $fetchCommonOperation->status ?>}</span>
                            </div>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
        </div>
        <?php
    }

    $fetchProfileHistoryWithFromToDate = $IB->request("fetchProfileHistoryWithFromToDate", [
        $IB->TOKEN,
        $operationHistoryFilter,
        ($historyFinanceData['modeFilter'] == "last") ? ($historyFinanceData['currentDateUnix'] - ($historyFinanceData['lastFilter'] * 3600 * 24)) * 1000 : ($historyFinanceData['dateFromUnix'] * 1000),
        ($historyFinanceData['modeFilter'] == "last") ? ($historyFinanceData['currentDateUnix'] + 3600 * 24) * 1000 : ($historyFinanceData['dateToUnix'] * 1000),
        new \Attributes()
    ]);

    if ($fetchProfileHistoryWithFromToDate) {
        ?>
        <div class="overview operations-table <?= (($tab == 3) ? "active" : "") ?> " data-tab_for="3">
            <div class="tabs">

                <table class="operations-table-desktop">
                    <thead>
                    <tr class="categories">
                        <th class="date">{CABINET-OPETATION-TABLE-DATE}</th>
                        <th>{CABINET-OPETATION-TABLE-OPERATION}</th>
                        <th>{USER-OPERATION-STATUS}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    foreach ($fetchProfileHistoryWithFromToDate as $item) {
                        $historyDateTime = isset($item->operationDate) ? $item->operationDate / 1000 : "";
                        $historyDate = date("d.m.Y", $historyDateTime);
                        $historyTime = date("H:i", $historyDateTime);
                        ?>
                        <tr class="operation">
                            <td class="date"><span><?= $historyDate ?></span><span><?= $historyTime ?></span></td>
                            <td><span>{USER-PROFILE-OPERATION-TYPE-<?= $item->profileOperationType ?>}</span></td>
                            <td class="user-operation-status-<?= $item->operationStatus ?>"><span>{USER-OPERATION-STATUS-<?= $item->operationStatus ?>}</span></td>
                        </tr>
                        <?php
                    }

                    ?>
                    </tbody>
                </table>

                <ul class="operations-table-mobile">
                    <?php
                    foreach ($fetchProfileHistoryWithFromToDate as $item) {
                        $historyDateTime = isset($item->operationDate) ? $item->operationDate / 1000 : "";
                        $historyDate = date("d.m.Y", $historyDateTime);
                        $historyTime = date("H:i", $historyDateTime);
                        ?>
                        <li class="col-xs-12">
                            <div class="col-xs-8">
                                <div class="money">
                                    {USER-PROFILE-OPERATION-TYPE-<?= $item->profileOperationType ?>}
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <span><?= $historyDate ?></span><span><?= $historyTime ?></span>
                                <span class="user-operation-status-<?= $fetchCommonOperation->status ?>"><span>{USER-OPERATION-STATUS-<?= $item->operationStatus ?>}</span>
                            </div>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
            </div>
        </div>
        <?php
    }
}
?>
</div>