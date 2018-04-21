<?php
$FinancialOperationTypeList = $IB -> CONSTANTS['FinancialOperationType'];

$account = isset($account) ? $account : false;
$type = isset($type) ? $type : false;

?>

<table class="operations-table-desktop">
    <thead>
    <tr class="categories">
        <th class="date">{CABINET-OPETATION-TABLE-DATE}</th>
        <th>{CABINET-OPETATION-TABLE-ACCOUNT}</th>
        <th>{CABINET-OPETATION-TABLE-SUBJECT}</th>
        <th class="money">{CABINET-OPETATION-TABLE-AMOUNT}</th>
        <!-- <th class="money remains">Залишок</th> -->
        <th class="actions-cat">{CABINET-OPETATION-TABLE-OPERATIONS}</th>
    </tr>
    </thead>
    <tbody>

    <?php
    foreach ($historyFinanceData['historyFinanceArr'] as $historyBlock) {
        ?>
        <tr class="operation">
            <td class="date"><span><?=$historyBlock['historyDate']?></span><span><?=$historyBlock['historyTime']?></span></td>
            <td><?=$historyBlock['mainAccountNumber']?></td>
            <td><span><?=$historyBlock['finOperationTitle']?></span></td>
            <td class="money">
                <span class="<?=$historyBlock['classColor']?>"><?=$tpl->priceFormat($historyBlock['amountInCents'])?></span>
                <span class="money_currency <?=$historyBlock['classColor']?>"><?=$historyBlock['currency']?></span>
            </td>
            <td class="actions-cat">
                <?php
                if (isset($historyBlock['historyItem'] -> type) && isset($FinancialOperationTypeList[$historyBlock['historyItem'] -> type]) && $historyBlock['operationID'])
                    include $tpl -> pathFull."/snippets/cabinet_finance_operations_repeat.php";

                include $tpl -> pathFull."/snippets/cabinet_finance_operations_view_receipt.php";

                if (isset($historyBlock['historyItem'] -> type))
                    include $tpl -> pathFullTemplate."/snippets/cabinet_finance_operations_print_receipt.php";
                ?>
            </td>
        </tr>
        <?php
    }
    ?>
    </tbody>
</table>

<ul class="operations-table-tablet">
    <?php
    foreach ($historyFinanceData['historyFinanceArr'] as $historyBlock) {
        ?>
        <li class="col-sm-12 history-block">
            <div class="col-sm-7">
                <div>
                    <span class="money <?=$historyBlock['classColor']?>">
                         <span class="<?=$historyBlock['classColor']?>"><?=$tpl->priceFormat($historyBlock['amountInCents'])?></span>
                    </span>
                    <span class="money money_currency">
                        <span class="<?=$historyBlock['classColor']?>"><?=$historyBlock['currency']?></span>
                    </span>
                </div>
                <div><span><?=$historyBlock['finOperationTitle']?></span></div>
            </div>
            <div class="col-sm-3">
                <span><?=$historyBlock['historyDate']?></span>
                <span><?=$historyBlock['historyTime']?></span>
                <span>success</span>
            </div>

            <div class="col-sm-2">

                <?php
                if (isset($historyBlock['historyItem'] -> type) && isset($FinancialOperationTypeList[$historyBlock['historyItem'] -> type]) && $historyBlock['operationID'])
                    include $tpl -> pathFull."/snippets/cabinet_finance_operations_repeat.php";

                include $tpl -> pathFull."/snippets/cabinet_finance_operations_view_receipt.php";

                if (isset($historyBlock['historyItem'] -> type))
                    include $tpl -> pathFull."/snippets/cabinet_finance_operations_print_receipt.php";
                ?>
            </div>
        </li>
        <?php
    }
    ?>
</ul>

<ul class="operations-table-mobile">
    <?php
    foreach ($historyFinanceData['historyFinanceArr'] as $historyBlock) {
        ?>
        <li class="col-xs-12">
            <div class="col-xs-8">
                <div>
                    <span class="money">
                        <span class="<?=$historyBlock['classColor']?>">
                            <?=$tpl->priceFormat($historyBlock['amountInCents'])?>
                        </span>
                    </span>
                    <span class="money money_currency <?=$historyBlock['classColor']?>">
                        <span class="<?=$historyBlock['classColor']?>">
                            <?=$historyBlock['currency']?>
                        </span>
                    </span>
                </div>
                <div><span><?=$historyBlock['finOperationTitle']?></span></div>
            </div>
            <div class="col-xs-4">
                <span><?=$historyBlock['historyDate']?></span>
                <span><?=$historyBlock['historyTime']?></span>
                <span>success</span>
            </div>
        </li>
        <?php
    }
    ?>
</ul>

