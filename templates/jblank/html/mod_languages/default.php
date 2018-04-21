<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_languages
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

$getUrl = isset($_SERVER['QUERY_STRING']) ?  ("?".$_SERVER['QUERY_STRING']) : "";

JHtml::_('stylesheet', 'mod_languages/template.css', array(), true);

if ($params->get('dropdown', 1) && !$params->get('dropdownimage', 0))
{
	JHtml::_('formbehavior.chosen');
}
?>
<div class="mod-languages<?php echo $moduleclass_sfx; ?>">
<?php if ($headerText) : ?>
	<div class="pretext"><p><?php echo $headerText; ?></p></div>
<?php endif; ?>

<?php if ($params->get('dropdown', 1) && !$params->get('dropdownimage', 0)) : ?>
	<form id="lang-form" name="lang" method="post" action="<?php echo htmlspecialchars(JUri::current(), ENT_COMPAT, 'UTF-8'); ?>">
	<select id="lang-select" class="inputbox advancedSelect" >
	<?php foreach ($list as $language) : ?>
        <?php
        $langLink = $language->link;
        $langLink = $langLink . (!strpos($langLink, $getUrl) ? $getUrl : "");
        ?>

		<option dir=<?php echo $language->rtl ? '"rtl"' : '"ltr"'; ?> value="<?php echo $langLink; ?>" <?php echo $language->active ? 'selected="selected"' : ''; ?>>
		<?php echo $language->title_native; ?></option>
	<?php endforeach; ?>
	</select>
        <?php
        if(!empty($_POST['SERVICE'])){
            ?>
            <input type="hidden" name="SERVICE" value="<?=$_POST['SERVICE']?>" />
            <?php
        }
        if(!empty($_POST['data'])){
            foreach ($_POST['data'] as $name => $value) {
                if(!is_array($value)){
                    if(!empty($value)){
                        ?>
                        <input type="hidden" name="data[<?=$name?>]" value="<?=$value?>" />
                    <?php
                    }
                }else{
                    foreach ($value as $parameterKey => $parameterValue) {
                        if(!is_array($parameterValue) && !empty($parameterValue)){
                            ?>
                            <input type="hidden" name="data[<?=$name.']['.$parameterKey.']'?>" value="<?=$parameterValue?>" />
                            <?php
                        }
                    }
                }
            }
        }
        ?>
	</form>
<script type="text/javascript">

    jQuery(document).ready(function () {
        (function($) {
            $('#lang-select').on('change', function () {
                $('#lang-form').attr('action', $(this).val());
                $('#lang-form').submit();
            });
        })(jQuery);
    });
</script>
<?php elseif ($params->get('dropdown', 1) && $params->get('dropdownimage', 0)) : ?>
	<div class="btn-group">
		<?php foreach ($list as $language) : ?>
			<?php if ($language->active) : ?>
				<?php $flag = ''; ?>
				<?php $flag .= "&nbsp;" . JHtml::_('image', 'mod_languages/' . $language->image . '.gif', $language->title_native, array('title' => $language->title_native), true); ?>
				<?php $flag .= "&nbsp;" . $language->title_native; ?>
				<a href="#" data-toggle="dropdown" class="btn dropdown-toggle"><span class="caret"></span><?php echo $flag; ?></a>
			<?php endif; ?>
		<?php endforeach;?>
		<ul class="<?php echo $params->get('lineheight', 1) ? 'lang-block' : 'lang-inline'; ?> dropdown-menu" dir="<?php echo JFactory::getLanguage()->isRtl() ? 'rtl' : 'ltr'; ?>">
		<?php foreach ($list as $language) : ?>
			<?php if ($params->get('show_active', 0) || !$language->active) : ?>
				<li class="<?php echo $language->active ? 'lang-active' : ''; ?>" >
				<a href="<?php echo $language->link;?>">
						<?php echo JHtml::_('image', 'mod_languages/' . $language->image . '.gif', $language->title_native, array('title' => $language->title_native), true); ?>
						<?php echo $language->title_native; ?>
				</a>
				</li>
			<?php endif; ?>
		<?php endforeach; ?>
		</ul>
	</div>
<?php else : ?>
	<ul class="<?php echo $params->get('inline', 1) ? 'lang-inline' : 'lang-block'; ?>">
	<?php foreach ($list as $language) : ?>
		<?php if ($params->get('show_active', 0) || !$language->active) : ?>
			<li class="<?php echo $language->active ? 'lang-active' : ''; ?>" dir="<?php echo $language->rtl ? 'rtl' : 'ltr'; ?>">
			<a href="<?php echo $language->link; ?>">
			<?php if ($params->get('image', 1)) : ?>
				<?php echo JHtml::_('image', 'mod_languages/' . $language->image . '.gif', $language->title_native, array('title' => $language->title_native), true); ?>
			<?php else : ?>
				<?php echo $params->get('full_name', 1) ? $language->title_native : strtoupper($language->sef); ?>
			<?php endif; ?>
			</a>
			</li>
		<?php endif; ?>
	<?php endforeach; ?>
	</ul>
<?php endif; ?>

<?php if ($footerText) : ?>
	<div class="posttext"><p><?php echo $footerText; ?></p></div>
<?php endif; ?>
</div>
