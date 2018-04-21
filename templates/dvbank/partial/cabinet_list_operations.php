<?php
$AllCard = $IB -> request("findAllCardContracts", [
    $IB->TOKEN,
    new \Attributes()
]);

$limit = isset($limit) ? $limit : false;

if ($AllCard) {
    ?>

    <li  class="li-grid-item">
        <div class="overview overview_products overview_products">
            <div class="overview__inner">
                <div class="services">
                    <div class="services__name">
<!--                        <i class="icons icons_chevron"></i>-->
                        <span>{CABINET-CARD-MYOPERATIONS}</span>
                    </div>
                </div>
                <div class="products products_cards cf custom__scroll--js">
                    <?php

                    $historyFinanceData = $IB -> getFinanceOperations();
                    $historyFinance = $historyFinanceData['historyFinance'];
                    $historyFinanceArr = $historyFinanceData['historyFinanceArr'];

                    $IB -> includes("blocks/finance_operations", ['historyFinanceData' => $historyFinanceData, 'limit' => $limit]);
                    ?>
                </div>
            </div>


        </div>
    </li>






    <?php
}

?>


