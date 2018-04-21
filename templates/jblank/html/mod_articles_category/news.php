<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_category
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

?>

<div class="news-items col-md-12 col-sm-12 col-xs-12 category-module<?php echo $moduleclass_sfx; ?>">
    <?php foreach($list as $item){
        $images = json_decode($item->images);
        ?>
<?php print_r($item) ?>
        <div class="news-item col-md-12 col-sm-12 col-xs-12">
            <div class="preview-image col-md-5 col-sm-5 col-xs-5">
                <img src="/<?=$images->image_intro?>">
            </div>
            <div class="preview-info col-md-7 col-sm-7 col-xs-7">
                <div class="preview-name">
                    <span><?=$item->title?></span>
                </div>
                <div class="preview-date">
                    <span><?=$newDate?></span>
                </div>
                <div class="preview-text">
                    <span><?=$NewsIntrotext?></span>
                </div>
                <div class="preview-more">
                    <a href="<?=$item->link?>" class="more__link"><i class="icons icons_back-arrow"></i><span><?=$read_more?></span></a>
                    <div class="icons__wrap">
                        <a href="https://www.facebook.com/sharer.php?u=<?=$item->link?>&t=<?=$item->title?>&src=sp"><i class="icons icons_fb"></i></a>
                        <a href="https://twitter.com/share?text=<?=$item->title?>&url=<?=$item->link?>"><i class="icons icons_tw"></i></a>
                    </div>
                </div>
            </div>
        </div>


    <?php } ?>
</div>


<div class="category-module<?php echo $moduleclass_sfx; ?>">
    <div class="services">

		<?php foreach ($list as $item) : ?>
            <?php


            JLoader::register('FieldsHelper', JPATH_ADMINISTRATOR . '/components/com_fields/helpers/fields.php');
            //$jcFields = FieldsHelper::getFields('com_content.article', $item, true);

//            foreach ($jcFields as $field){
//                if ($field->name == 'short-story'){
//                    $shortStory = $field->value;
//                }
//            }

            ?>
            <div class="services__item col-lg-6 col-md-6 col-sm-6 col-xs-12">
                <div class="bgimage bgimage-first" style="background: url('/<?=$images->image_intro?>');"></div>
                <div class="bgimage bgimage-second" style="background: url('/<?=$images->image_fulltext?>');"></div>
                <h2><?=$item->title?></h2>
                <div class="preview"><?=$shortStory?></div>
                <a href="<?=$item->link?>" class="btn_transparent">Докладніше</a>



            </div>

		<?php endforeach; ?>
    </div>
</div>
