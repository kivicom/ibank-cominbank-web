<?php
/**
 * J!Blank Template for Joomla by JBlank.pro (JBZoo.com)
 *
 * @package    JBlank
 * @author     SmetDenis <admin@jbzoo.com>
 * @copyright  Copyright (c) JBlank.pro
 * @license    http://www.gnu.org/licenses/gpl.html GNU/GPL
 * @link       http://jblank.pro/ JBlank project page
 */

defined('_JEXEC') or die;

$CONFIG = \JFactory::getConfig();
define('TEMPLATE', $CONFIG -> get('TEMPLATE'));

// init $tpl helper
require_once dirname(__FILE__) . '/php/init.php';
require_once "php/IBModule.php";
require_once "php/libs/simplehtmldom/simple_html_dom.php";
require_once "php/voku/helper/AntiXSS.php";
$getApplication = JFactory::getApplication();
$pageClassSuffix = $getApplication->getMenu()->getActive()? $getApplication->getMenu()->getActive()->params->get('pageclass_sfx', '-default') : '-default';

$CURRENT_PAGE_ID = (JRequest::getVar('option')==='com_content' && JRequest::getVar('view')==='article')? JRequest::getInt('id') : 0;
$CURRENT_PAGE  = JTable::getInstance('Content', 'JTable');
$CURRENT_PAGE_URL = $tpl -> getUrlPage($CURRENT_PAGE_ID);
$CURRENT_PAGE->load(array('id'=>$CURRENT_PAGE_ID));

define("PAGE_ID", $CURRENT_PAGE_ID);

$jinput = $getApplication->input;

$option = $jinput->getCmd('option'); // This gets the component
$view   = $jinput->getCmd('view');   // This gets the view
$layout = $jinput->getCmd('layout'); // This gets the view's layout

define("IS_CATEGORY", ($option == 'com_content' && $view == 'category') ? true : false);    //Если находимся на странице категории, то true

$langJ = JFactory::getLanguage();
$languagesJ = JLanguageHelper::getLanguages('lang_code');
$languageTag = $langJ->getTag();

$I18N = parse_ini_file($_SERVER['DOCUMENT_ROOT']."/language/overrides/".$languageTag.".override.ini");

$IB = new tutorial\php\IBClass();

$GLOBALS['IB'] = $IB;

$tpl -> AuthorizationAPI();

$getCurrentMenuIds = $tpl -> getAllParentsOfCurrentPage();
define("MENU_ID",count($getCurrentMenuIds) ? end($getCurrentMenuIds) : "");
define("BREADCRUMBS", $getCurrentMenuIds);
define("TIME", time());

$IB -> log("**================================================================================**".PHP_EOL.PHP_EOL);

//Если находимся на страницах кабинета, предварительно выполняется getAuthSession для проверки авторизации
if (
        (MENU_ID != 116) &&
        (MENU_ID != 109) &&
        ((BREADCRUMBS[0] == 108) or (BREADCRUMBS[0] == 115)) &&
        count(BREADCRUMBS) >= 2
) {
//    $getAuthSession = $IB -> request("getAuthSession", [
//        $IB->TOKEN
//    ]);
//    $IB -> EXCEPTION_process();
}


//Интернационализация
foreach ($I18N as $field=>$value) { $I18N["{".$field."}"] = $value; unset($I18N[$field]); }
define("I18N", $I18N);

$tpl -> processAjaxTemplate();

$art_content = JTable::getInstance('content');
$art_content->load($CURRENT_PAGE_ID);
$ATTRS = json_decode($art_content -> attribs);

$params = array("IB" => $IB, "I18N" => I18N);
$htmlPage = '';

if ($ATTRS -> show_intro !== "0") $htmlPage .= $tpl->partial("header", $params);   //Если в Отображении страницы не указан "Вводный текст" -> скрыть

if (IS_CATEGORY) {
    $db = JFactory::getDBO();
    $catid = JRequest::getInt('id');
    $db->setQuery("SELECT description FROM #__categories WHERE id = ".$catid." LIMIT 1;");
    $htmlPage .= $db->loadResult();
} else {
    $htmlPage .= $CURRENT_PAGE->introtext;
}

if ($ATTRS -> show_intro !== "0") {                     //Если в Отображении страницы не указан "Вводный текст" -> скрыть
    $htmlPage .= $tpl->partial("footer", $params);
    $htmlPage .= $tpl->partial("modal_ext_auth", $params);  //Двухэтапная авторизация
}

//Применение шаблонов
$htmlPage = $tpl -> ApplyTemplatesIB($htmlPage, $tpl, $params);

$htmlPage = strtr($htmlPage, I18N);

echo $tpl->renderHTML();

if ($ATTRS -> show_intro !== "0") {
    $document = JFactory::getDocument();
    $headData = $document->getHeadData();

    $scripts = $headData['scripts'];
    unset($scripts['/media/jui/js/bootstrap.min.js']);
    $headData['scripts'] = $scripts;
    $document->setHeadData($headData);

    $CSS = [];
    $listCSS = scandir($tpl -> pathFull."/css/");
    foreach ($listCSS as $index => $listItem) if ((strpos($listItem, ".css") !== false) or (strpos($listItem, ".scss") !== false)) $CSS[] = $tpl -> css."/".$listItem;

    $listCSS = scandir($tpl -> pathFullTemplate."/css/");
    foreach ($listCSS as $index => $listItem) if ((strpos($listItem, ".css") !== false) or (strpos($listItem, ".scss") !== false)) $CSS[] = $tpl -> pathTemplate."/css/".$listItem;

    $JS = [];
    $listJS = scandir($tpl -> pathFull."/js/");
    foreach ($listJS as $index => $listItem) if (strpos($listItem, ".js") !== false) $JS[] = $tpl -> js."/".$listItem;

    $listJS = scandir($tpl -> pathFullTemplate."/js/");
    foreach ($listJS as $index => $listItem) if (strpos($listItem, ".js") !== false) $JS[] = $tpl -> pathTemplate."/js/".$listItem;

    $tpl -> css($CSS);
    $tpl -> js($JS);

    if ($IB -> CONFIG -> get('CssJsMinimize')){
        $tpl -> merge('css');
        $tpl -> merge('js');
    }


?>
    <head>
        <jdoc:include type="head" />
        <link href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&amp;subset=cyrillic" rel="stylesheet">

    </head>

    <body class="<?php echo $tpl->getBodyClasses(); ?> <?php echo $pageClassSuffix ?>">

    <input type="hidden" id="service_information"
           data-pageid="<?=$CURRENT_PAGE_ID?>"
           data-pageurl="<?=$CURRENT_PAGE_URL?>"
           data-lang="<?=$tpl -> lang?>"
           data-scroll="<?=(isset($_GET['scroll']) ? $_GET['scroll'] : 0)?>"
    />
    <?php
} else {
    ?>
    <head></head>
    <body>
    <?php
}

?>
    <?=$htmlPage?>

    </body>
    </html>
<?php

$IB -> SESS['REFER'] = "$_SERVER[REQUEST_URI]";
$IB -> SESSION();

$IB -> EXCEPTION_process();


?>



