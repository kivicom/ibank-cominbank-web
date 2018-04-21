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

<div class="visible-md visible-sm visible-xs">
    <div id="sidebar_mobile">
        <div class="toggle_sidebar">
            <ul><li></li><li></li><li></li></ul>
        </div>

        [cabinet_sidebar][/cabinet_sidebar]
        <div class="sidebar_mobile_swipe"></div>
    </div>
    <div class="toggle_sidebar">
        <ul><li></li><li></li><li></li></ul>
    </div>
</div>

<header class="fixed-header">
    <nav class="fixed-header-navigation">
        <ul class="fixed-header__nav left">
            <li>
                <a href="<?=$IB -> CONFIG -> get('URL_WEBSITE')?>" class="header__button header__button_with-image btn-frame authorization_website">
                    <div class="icons">
                        <img src="<?=$tpl->pathTemplate?>/img/content/dvbank/back-to.png">
                    </div>
                    <span class="text">{HEADER-BACK-TO}</span>
                </a>
            </li>
            <li>
                <a href="<?=$IB -> CONFIG -> get('URL_WEBSITE_FOREX')?>" class="header__button header__button_with-image header__button_white authorization_website">
                    <div class="icons">
                        <img src="<?=$tpl->pathTemplate?>/img/content/dvbank/forex.svg">
                    </div>
                    <span class="text">Forex</span>

                </a>
            </li>
            <li>
                <a href="<?=$IB -> CONFIG -> get('URL_WEBSITE_RADIO')?>" class="header__button header__button_with-image header__button_whit authorization_websitee">
                    <div class="icons">
                        <img src="<?=$tpl -> pathTemplate?>/img/content/dvbank/radio.svg">
                    </div>


                    <span class="text">Radio</span>
                </a>
            </li>
        </ul>

        <div class="fixed-header__logo">
            <a href="/<?=$tpl->lang?>/cabinet/products">
                <img src="<?=$tpl->pathTemplate?>/img/content/dv_logo.png">
            </a>
        </div>
        <ul class="fixed-header__nav right">

            <li class="exit-enter">
                <?php if (!empty($resultUserInfo)){ ?>
                    <a href="<?=$tpl->lang?>/cabinet/auth?logout" class="header__button btn-frame">
                        <span class="text">{LOGIN-OUT}</span>
                    </a>
                <?php } else { ?>
                    <a href="/cabinet/auth" class="header__button btn-frame">
                        <span class="text">{LOGIN-IN}</span>
                    </a>
                <?php } ?>
            </li>
        </ul>
        <ul class="fixed-header__nav right">
            <li>
                <div class="info-center">
                    <span>{HEADER-INFO-CENTER}</span>
                    <a href="tel:<?=$IB -> CONFIG -> get('PHONE_NUMBER')?>"><?=$IB -> CONFIG -> get('PHONE_NUMBER')?></a>
                </div>
            </li>
            <li>
                <div class="header__button header__button_white header__button_icon-only share">
                    <div class="icons">
                        <img src="<?=$tpl->pathTemplate?>/img/content/dvbank/share.png">
                    </div>

                    <div class="social-list">
                        <a href="<?=$IB -> CONFIG -> get('URL_FACEBOOK')?>" target="_blank">
                            <img src="<?=$tpl->pathTemplate?>/img/content/dvbank/facebook.svg" title="Facebook" alt="Facebook">
                        </a>
                    </div>
                </div>
            </li>
            <li>
                <div class="header__button header__button_white header__button_icon-only search-button">

                    <div class="search-popup">
                        <form method="get" action="<?=$IB -> CONFIG -> get('URL_WEBSITE_SEARCH')?>" id="search-form">
                            <input type="text" name="q" placeholder="{HEADER-SEARCH}" value="">
                            <button type="submit"></button>
                        </form>
                    </div>
                </div>

            </li>

            <li>
                <div class="langs">
                    <jdoc:include type="modules" name="language" />
                </div>
            </li>
        </ul>

    </nav>
    <?php if (!empty($resultUserInfo)){ ?>
        <div class="client__name-dv"><span class="user--greet">{HEADER-GREETING},</span>
            <a class="user--name" href="/<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_PROFILE_URL'))?>"><?=$resultUserInfo?>!</a>
            <?php if (!empty($resultUserInfo)){ ?>
                <a href="<?=$tpl->lang?>/cabinet/auth?logout" class="header__button btn-frame">
                    <span class="text">{LOGIN-OUT}</span>
                </a>
            <?php } else { ?>
                <a href="/cabinet/auth" class="header__button btn-frame">
                    <span class="text">{LOGIN-IN}</span>
                </a>
            <?php } ?>
        </div>
    <?php } ?>

</header>

<script>
    (function ($) {
        let searchButton = $('.search-button');

        searchButton.click(function () {
            $('.search-popup').addClass('open');
        });
        $(document).mouseup(function (e) {

            if ($('.search-popup').has(e.target).length === 0){
                $('.search-popup').removeClass('open');
            }
        });


        $('#search-form').submit(function (e) {
            var auth = <?=($IB -> TOKEN ? "true" : "false")?>;
            if (auth) {
                var searchquery = $(this).serialize();
                e.preventDefault();
                jQuery("#authorization_website input[name='redirect_url']").val('<?=$IB -> CONFIG -> get('URL_WEBSITE')?>/search?'+searchquery);
                jQuery("#authorization_website").submit();
            }
        });

    }(jQuery));
</script>

<!-- вывести публикации на страницах категории -->
<?php
/*
$jinput = JFactory::getApplication()->input;

$option = $jinput->getCmd('option'); // This gets the component
$view   = $jinput->getCmd('view');   // This gets the view
$layout = $jinput->getCmd('layout'); // This gets the view's layout

if ($option == 'com_content' && $view == 'category')
{
    ?>
    <jdoc:include type="component"/>
    <?php
}
*/
?>