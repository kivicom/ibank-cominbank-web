<?php
$resultUserInfo = "";

if ($IB->TOKEN) {
    $result = $IB -> request("getAuthSession", [
        $IB->TOKEN
    ]);
    $resultUserInfo = isset($result->userInfo->fullName) ? $result->userInfo->fullName : "";
}
?>

<div class="sidebar">
    <?php
    if ($resultUserInfo) {
        ?>

        <jdoc:include type="modules" name="position-sidebar" />
        <?php
    } else {
        ?>
<!--        <jdoc:include type="modules" name="menu-left-ua" />-->
    <?php
    }
    ?>
    <jdoc:include type="modules" name="position-sidebar-bottom" />

<!--    <ul class="sidebar-bottom">-->
<!--        <li>-->
<!--            <a href="https://dvbank.vis-design.com/privatnim-kliyentam/groshovi-perekazi/perekaz-z-karti-na-kartu">Переказ з карти на карту-->
<!--                <img src="https://dvbank.vis-design.com/storage/editor/fotos/perekaz-z-karti-na-kartu_1510232434767.svg">-->
<!--            </a>-->
<!--        </li>-->
<!--        <li>-->
<!---->
<!--            <a href="https://web-dev-dvbank.payforce.net.ua/uk/cabinet/remittance"><img src="https://dvbank.vis-design.com/storage/editor/fotos/oplata-poslug_1510232477610.svg">Оплата послуг</a>-->
<!--        </li>-->
<!--        <li>-->
<!---->
<!--            <a href="https://dvbank.vis-design.com/ocinka-yakosti-obslugovuvannya"><img src="https://dvbank.vis-design.com/storage/editor/fotos/ocinka-yakosti-obslugovuvannya_1509719310820.svg">Оцінка якості обслуговування</a>-->
<!--        </li>-->
<!--        <li>-->
<!---->
<!--            <a href="#">Зворотній зв`язок-->
<!--                <img src="https://dvbank.vis-design.com/storage/settings/0fb0a4f0edd44ef222e3ae1ca5ed588a.svg"></a>-->
<!--        </li>-->
<!---->
<!--    </ul>-->

</div>
<style>
    .sidebar__list a:after {
        content: '';
        display: none;
    }
    .sidebar__list a img {
        position: absolute;
        top: 10px;
        left: 50%;
        transform: translate(-50%);
    }

    .sidebar-bottom a:after {
        position: absolute;
        content: '';
        display: none;
    }
    .sidebar-bottom a img {
        position: absolute;

        left: 15px;
        top: 50%;
        -webkit-transform: translateY(-50%);
        transform: translateY(-50%);
    }
</style>