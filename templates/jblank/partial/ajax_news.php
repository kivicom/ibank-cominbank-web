<?php
/**
 * Created by PhpStorm.
 * User: Alex
 * Date: 29.06.2017
 * Time: 11:19
 */

$JBlankTemplate = new JBlankTemplate();
$AllNews = $JBlankTemplate->getMaterialsOfCategory($_GET['catid']);
echo $allNewsCount = count($AllNews);
$currentPage = (!empty($_GET['page'])? $_GET['page']: 1); // получаем номер постраничной навигации для новостей
$newsFrom = ($currentPage-1)*$page_limit;
$nextNewsIndex = $newsFrom+$page_limit;
// проверяем, существует ли хотя бы один элемент в для следующего среза - lastpage or not
$islastpage = ($AllNews[$nextNewsIndex]?false:true);
$AllNews = array_slice($AllNews,$newsFrom,$page_limit);
foreach ($AllNews as $newsItem){
    $NewsUrl = $JBlankTemplate->getUrlPage($newsItem->id);
    $NewsImageUrl = (json_decode($newsItem->images, true))[image_intro];
    $NewsTitle = $newsItem->title;
    $NewsDate = $newsItem->publish_up;

    $langText = $JBlankTemplate->langText($tpl->lang);
    $weekdays = $langText['weekdays'];
    $read_more = $langText['read_more'];

    $exp_date = getdate(strtotime($NewsDate));
    $newDate = sprintf(
        '%d %s %d %d:%d',
        $exp_date['mday'],
        $weekdays[$exp_date['mon']],
        $exp_date['year'],
        $exp_date['hours'],
        $exp_date['minutes']
    );
    $NewsIntrotext = $newsItem->introtext;
    ?>
    <div class="news-item col-md-12 col-sm-12 col-xs-12">
        <div class="preview-image col-md-5 col-sm-5 col-xs-5">
            <img src="/<?=$NewsImageUrl?>">
        </div>
        <div class="preview-info col-md-7 col-sm-7 col-xs-7">
            <div class="preview-name">
                <span><?=$newsItem->title?></span>
            </div>
            <div class="preview-date">
                <span><?=$newDate?></span>
            </div>
            <div class="preview-text">
                <span><?=$NewsIntrotext?></span>
            </div>
            <div class="preview-more">
                <a href="<?=$NewsUrl?>" class="more__link"><i class="icons icons_back-arrow"></i><span><?=$read_more?></span></a>
                <div class="icons__wrap">
                    <a href="https://www.facebook.com/sharer.php?u=<?=$NewsUrl?>&t=<?=$newsItem->title?>&src=sp"><i class="icons icons_fb"></i></a>
                    <a href="https://twitter.com/share?text=<?=$newsItem->title?>&url=<?=$NewsUrl?>"><i class="icons icons_tw"></i></a>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>

