<?php

$resultUserInfo = "";
if ($IB->TOKEN) {
    $result = $IB -> request("getAuthSession", [
        $IB->TOKEN
    ]);
    $resultUserInfo = isset($result->userInfo->fullName) ? $result->userInfo->fullName : "";
}

$IB -> includes("blocks/authorization_website");
?>

<div class="header header-zoom-js">

<!--    <div class="hamburger-button">-->
<!--        <img src="dev/static/img/minified-svg/show-menu-button.svg" alt="hamburger button">-->
<!--    </div>-->

    <div class="container">
        <a href="<?=$IB -> CONFIG -> get('URL_WEBSITE')?>" class="logo authorization_website">
            <img src="<?=$tpl -> pathTemplate?>/img/content/logo.svg" alt="Industrial Bank">
        </a>
        <nav class="main-nav">
            <ul>
                <li class="search--mob">
                    <form class="search-item">
                        <input class="header-search" placeholder="Пошук">
                        <button class="icon__search" type="submit"><img src="<?=$tpl -> pathTemplate?>/img/svg-sprite/search.svg" alt="Icon-search"></button>
                    </form>

                </li>
                <li class="header-mobile_hidden">
                    <ul class="mini-nav">
                        <li>
                            <a class="head-wrap" href="/uk/departments">
                                <div class="icons-back">
                                    <img src="/templates/jblank/images/icons/mappos.svg" class="icons icons_header">
                                </div>
                                <span>Відділення та банкомати</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li class="header-mobile_hidden">
                    <a class="to-exchange-js head-wrap" href="#" data-toggle="modal" data-target="#exchange_modal">
                        <div class="icons-back">
                            <img src="/templates/jblank/images/icons/exchange.svg" class="icons icons_header">
                        </div>
                        <span>{CABINET-EXCHANGE-CONVERTER}</span>
                    </a>
                </li>
                <li class="header-mobile_hidden">
                    <jdoc:include type="modules" name="language" />
                </li>
                <li class="act--mob">
                    <div class="head-wrap">
                        <?php
                        if (!empty($resultUserInfo)){
                            ?>

                            <a href="<?=$tpl->lang?>/cabinet/auth?logout">
                                <img src="/static/img/minified-svg/exit.svg" class="icons icons_header">
                            </a>

                            <?php
                        }

                        if (!$IB -> TOKEN && MENU_ID != 116 && MENU_ID != 109) {
                            ?>
                            <a class="header-button" href="/<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_PROFILE_URL'))?>">
                                {LOGIN-IN}
                            </a>
                            <?php
                        }
                        ?>
                    </div>
                </li>
                <li class="header-mobile_hidden">
                    <a class="head-wrap" href="tel:+380800503535">
                        <div class="icons-back">
                            <img src="<?=$tpl -> pathTemplate?>/img/content/pays/phone.svg" class="icons icons_header" width="18px" height="18px">
                        </div>
                        <span>0 800 50 35 35</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</div>