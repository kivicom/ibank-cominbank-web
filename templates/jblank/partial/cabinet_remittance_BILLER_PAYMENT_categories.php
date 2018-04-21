<?php
$cats = isset($_GET['data']['cat']) ? $_GET['data']['cat'] : [];

$fetchBillerCategories = $IB->request("fetchBillerCategories", [
    $IB->TOKEN,
    null
]);

if (!empty($fetchBillerCategories)) {
    ?>
    <div class="billers-content">
        <ul class="billers_cat_list">
            <?php
            foreach ($fetchBillerCategories as $item) {
                include $tpl->pathFull . "/snippets/biller_category_icon.php";
                $URL = '/'. $tpl->lang . $IB->CONFIG->get('CABINET_REMITTANCE_URL') . '?FinancialOperationType=' . $FinancialOperationTypeActive . '&data[cat][0]=' . $item->id;
                ?>
                <li class="billers_cat_list__item <?= (in_array($item->id, $cats) ? "active" : "") ?>">
                    <a href="<?=$URL?>">
                        <div class="billers_cat_list__img">
                            <img src="<?= $filename ?>" alt="<?= $item->id ?>"/>
                        </div>
                        <div class="billers_cat_list__title"><?= $item->title ?></div>
                    </a>
                </li>
                <?php
            }
            ?>
        </ul>
    </div>

    <?php
    if (is_array($cats) && count($cats)) {
        foreach ($cats as $cat) {
            $fetchBillerCategoryPath = $IB->request("fetchBillerCategoryPath", [
                $IB->TOKEN,
                $cat
            ]);

            if (!empty($fetchBillerCategories)) {
                $fetchBillerCategoryPath = end($fetchBillerCategoryPath);

                $fetchBillerCategories1 = $IB->request("fetchBillerCategories", [
                    $IB->TOKEN,
                    $cat
                ]);

                if (!empty($fetchBillerCategories1)) {
                    ?>
                    <div class="billers-content">
                        <ul class="billers_cat_list">
                            <?php
                            foreach ($fetchBillerCategories1 as $item) {
                                include $tpl->pathFull . "/snippets/biller_category_icon.php";
                                $URL = '/'. $tpl->lang . $IB->CONFIG->get('CABINET_REMITTANCE_URL') . '?FinancialOperationType=' . $FinancialOperationTypeActive . '&data[cat][0]=' . $cats[0] . '&data[cat][1]=' . $item->id;
                                ?>
                                <li class="billers_cat_list__item <?= (in_array($item->id, $cats) ? "active" : "") ?>">
                                    <a href="<?=$URL?>">
                                        <div class="billers_cat_list__img">
                                            <img src="<?= $filename ?>" alt="<?= $item->id ?>"/>
                                        </div>
                                        <div class="billers_cat_list__title"><?= $item->title ?></div>
                                    </a>
                                </li>

                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                    <?php
                }
            }
        }
    }

    $currentCat = (is_array($cats) && count($cats)) ? end($cats) : null;

    if ($currentCat !== null) {
        $findBillers = $IB->request("findBillers", [
            $IB->TOKEN,
            null,
            $currentCat,
            null,
            0,
            10000,
            null
        ]);

        if ($findBillers && is_array($findBillers) && count($findBillers)) {
            ?>
            <ul class="billers_list">
                <?php
                foreach ($findBillers as $biller) {
                    ?>
                    <li class="billers_list__item">
                        <a href="/<?= $tpl->lang . $IB->CONFIG->get('CABINET_REMITTANCE_URL') ?>?FinancialOperationType=<?= $FinancialOperationTypeActive ?>&data[billerId]=<?=urlencode($biller->id)?>&data[providerId]=<?=urlencode($biller->providerId) ?>">
                            <div class="billers_list__img">
                                <?php
                                if ($biller->iconRef):
                                    ?>
                                    <img src="<?= $IB->CONFIG->get('BILLERS_ICONS') ?><?= $biller->iconRef . ".png" ?>"
                                         alt="<?= $biller->iconRef ?>"/>
                                    <?php
                                else:
                                    ?>
                                    <img src="<?= $tpl->img . "/icons/billers/default.png" ?>"
                                         alt="<?= $biller->iconRef ?>"/>
                                    <?php
                                endif;
                                ?>
                            </div>
                            <div class="billers_list__title"><?= $biller->title ?></div>
                        </a>
                    </li>
                    <?php
                }
                ?>
            </ul>
            <?php
            $IB->includes("blocks/back", ['link' => "/".$tpl->lang . $IB->CONFIG->get('CABINET_REMITTANCE_URL')."/?FinancialOperationType=". $FinancialOperationTypeActive]);
        }
    }
}