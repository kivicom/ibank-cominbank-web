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
        <!-- <div class="bottom__client">
            <a href="">
                <div class="client__img"><img src="/dev/static/img/content/5.png"></div>
                <span class="client__name"><?=$resultUserInfo?></span>
            </a>
        </div> -->
        <jdoc:include type="modules" name="menu-left" />
        <?php
    } else {
        ?>
        <jdoc:include type="modules" name="menu-left-ua" />
    <?php
    }
    ?>


</div>
