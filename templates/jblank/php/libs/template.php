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

// load joomla libs
jimport('joomla.filesystem.folder');
jimport('joomla.filesystem.file');

// load own libs
$tmplPath = dirname(__FILE__);
require_once $tmplPath . '/css.php';
require_once $tmplPath . '/css.less.leafo.php';
require_once $tmplPath . '/css.less.gpeasy.php';
require_once $tmplPath . '/css.scss.leafo.php';
require_once $tmplPath . '/minify.php';
require_once $tmplPath . '/class.mobiledetect.php';

/**
 * Class JBlankTemplate
 */
class JBlankTemplate
{
    /**
     * @var JDocumentHTML
     */
    public $doc = null;

    /**
     * @var Joomla\Registry\Registry
     */
    public $config = null;

    /**
     * @var JUri
     */
    public $url;

    /**
     * @var JApplicationSite
     */
    public $app;

    /**
     * @var JMenuSite
     */
    public $menu;

    /**
     * @var Joomla\Registry\Registry
     */
    public $params;

    /**
     * @var Joomla\Registry\Registry
     */
    public $request;

    /**
     * @var JUser
     */
    public $user;

    /**
     * @var JBlankMobileDetect
     */
    public $mobile;

    /**
     * @var string
     */
    public $dir;
    public $baseurl;
    public $path;
    public $pathFull;
    public $fonts;
    public $fontsFull;
    public $img;
    public $imgFull;
    public $less;
    public $lessFull;
    public $scss;
    public $scssFull;
    public $css;
    public $cssFull;
    public $js;
    public $jsFull;
    public $lang;
    public $langDef;

    /**
     * @var bool
     */
    protected $_debugMode = false;

    /**
     * Create and get instance
     * @return JBlankTemplate
     */
    public static function getInstance()
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Init internal vars
     */
    private function __construct()
    {
        $path = pathinfo($this->_getTemplatePathFull(), PATHINFO_BASENAME);

        // get links to global vars
        $this->doc = JFactory::getDocument();
        $this->config = JFactory::getConfig();
        $this->url = JUri::getInstance();
        $this->app = JFactory::getApplication();
        $this->menu = $this->app->getMenu();
        $this->params = $this->app->getTemplate(true)->params;
        $this->user = JFactory::getUser();
        $this->baseurl = $this->_getBaseUrl();

        // relative paths
        $this->path = $this->_getTemplatePath();
        $this->pathRoot = rtrim($this->baseurl, '/') . '/templates/';
        $this->pathFullRoot = rtrim(realpath(__DIR__ . '/../../../'), '/')."/";

        $this->pathTemplate = rtrim($this->baseurl, '/') . '/templates/' . TEMPLATE;

        $this->img = $this->path . '/images';
        $this->fonts = $this->path . '/fonts';
        $this->less = $this->path . '/less';
        $this->scss = $this->path . '/scss';
        $this->css = $this->path . '/css';
        $this->js = $this->path . '/js';

        // absolute paths
        $this->pathFull = $this->_getTemplatePathFull();
        $this->pathFullTemplate = rtrim(realpath(__DIR__ . '/../../../'), '/'). "/".TEMPLATE;

        $this->imgFull = JPath::clean($this->pathFull . '/images');
        $this->imgFullTemplate = JPath::clean($this->pathFullTemplate . '/images');

        $this->fontsFull = JPath::clean($this->pathFull . '/fonts');
        $this->fontsFullTemplate = JPath::clean($this->imgFullTemplate . '/fonts');

        $this->cssFull = JPath::clean($this->pathFull . '/css');
        $this->cssFullTemplate = JPath::clean($this->imgFullTemplate . '/css');

        $this->lessFull = JPath::clean($this->pathFull . '/less');
        $this->lessFullTemplate = JPath::clean($this->imgFullTemplate . '/less');

        $this->scssFull = JPath::clean($this->pathFull . '/scss');
        $this->scssFullTemplate = JPath::clean($this->imgFullTemplate . '/scss');

        $this->jsFull = JPath::clean($this->pathFull . '/js');
        $this->jsFullTemplate = JPath::clean($this->imgFullTemplate . '/js');

        $this->partial = JPath::clean($this->pathFull . '/partial');
        $this->theme = TEMPLATE;

        $this->partialTheme = JPath::clean($this->pathFullTemplate . "/" . 'partial');

        // init template vars
        $this->lang = $this->_getLangCurrent();
        $this->langDef = $this->_getLangDefault();
        $this->request = $this->_getRequest();
        $this->dir = $this->doc->getDirection();

        // init mobile detect
        $this->mobile = $this->_getMobile();

        $this->_debugMode = defined('JDEBUG') && JDEBUG;
    }

    /**
     * Create joomla module.
     * @param $name
     * @param string $style
     * @return string
     */
    public function module($name, $style = 'no')
    {
        return '<jdoc:include type="modules" name="' . $name . '" style="' . $style . '" />';
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->_debugMode;
    }

    /**
     * Get var from request
     * @param $key
     * @param null $default
     * @param string $filter
     * @return mixed
     */
    public function request($key, $default = null, $filter = 'cmd')
    {
        $jInput = JFactory::getApplication()->input;
        return $jInput->get($key, $default, $filter);
    }

    /**
     * @param $filename
     * @param string $prefix
     * @param string $type
     * @return $this
     */
    public function css($filename, $type = 'all', $prefix = '')
    {
        if (is_array($filename)) {
            foreach ($filename as $file) {
                $this->css($file, $type, $prefix);
            }

        } else if ($filename) {

            $ext = $this->_getExtension($filename);
            $prefix = (!empty($prefix) ? $prefix . '_' : '');

            if ($ext == 'css') {

                // include external
                if ($this->_isExternal($filename)) {
                    $this->doc->addStylesheet($filename, 'text/css', $type);
                    return $this;
                }

                // include in css folder
                $path = JPath::clean($this->cssFull . '/' . $prefix . $filename);
                if ($mtime = $this->_checkFile($path)) {
                    $cssPath = $this->css . '/' . $prefix . $filename . '?' . $mtime;
                    $this->doc->addStylesheet($cssPath, 'text/css', $type);
                    return $this;
                }

                // include related root site path
                $path = JPath::clean(JPATH_ROOT . '/' . $filename);
                if ($mtime = $this->_checkFile($path)) {
                    $cssPath = rtrim($this->baseurl, '/') . '/' . ltrim($filename, '/') . '?' . $mtime;
                    $this->doc->addStylesheet($cssPath, 'text/css', $type);
                    return $this;
                }

            } else if ($ext == 'less') {

                $lessMode = $this->params->get('less_processor', 'leafo');

                $path = JPath::clean($this->lessFull . '/' . $prefix . $filename);
                if ($this->_checkFile($path)) {
                    if ($cssPath = JBlankCss::getProcessor('less.' . $lessMode, $this)->compile($path)) {
                        $this->doc->addStylesheet($cssPath, 'text/css', $type);
                    }
                }

            } else if ($ext == 'scss') {

                if ($this->_isExternal($filename))
                    $path = strtr($filename, array($this -> pathRoot => $this -> pathFullRoot));
                else
                    $path = JPath::clean($this->scssFull . '/' . $prefix . $filename);

                if ($this->_checkFile($path)) {
                    if ($cssPath = JBlankCss::getProcessor('scss.leafo', $this)->compile($path)) {
                        $this->doc->addStylesheet($cssPath, 'text/css', $type);
                    }
                }
            }

        }

        return $this;
    }

    /**
     * @param $filename
     * @param string $prefix
     * @param bool $defer
     * @param bool $async
     * @return $this
     */
    public function js($filename, $prefix = '', $defer = false, $async = false)
    {
        if (is_array($filename)) {
            foreach ($filename as $file) {
                $this->js($file, $prefix, $defer, $async);
            }

        } else if ($filename) {

            $prefix = (!empty($prefix) ? $prefix . '_' : '');
            $path = JPath::clean($this->jsFull . '/' . $prefix . $filename);

            if ($this->_isExternal($filename)) {
                $this->doc->addScript($filename, "text/javascript", $defer, $async);

            } else if ($mtime = $this->_checkFile($path)) {
                $filePath = $this->js . '/' . $prefix . $filename . '?' . $mtime;
                $this->doc->addScript($filePath, "text/javascript", $defer, $async);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getBodyClasses()
    {
        return implode(' ', array(
            'tmpl-' . $this->request->get('tmpl', 'index'),
            'itemid-' . $this->request->get('Itemid', 0),
            'lang-' . $this->lang,
            'com-' . str_replace('com_', '', $this->request->get('option')),
            'view-' . $this->request->get('view', 'none'),
            'layout-' . $this->request->get('layout', 'none'),
            'task-' . $this->request->get('task', 'none'),
            'zoo-itemid-' . $this->request->get('item_id', 0),
            'zoo-categoryid-' . $this->request->get('category_id', 0),
            'device-ios-' . ($this->isiOS() ? 'yes' : 'no'),
            'device-android-' . ($this->isAndroidOS() ? 'yes' : 'no'),
            'device-mobile-' . ($this->isMobile() ? 'yes' : 'no'),
            'device-table-' . ($this->isTablet() ? 'yes' : 'no'),
        ));
    }

    /**
     * @return string
     */
    public function renderHTML()
    {
        $html = array(
            '<!doctype html>',

            '<!--[if lt IE 7]><html class="no-js lt-ie9 lt-ie8 lt-ie7 ie6 oldie" '
            . 'lang="' . $this->lang . '" dir="' . $this->dir . '"> <![endif]-->',

            '<!--[if IE 7]><html class="no-js lt-ie9 lt-ie8 ie7 oldie" '
            . 'lang="' . $this->lang . '" dir="' . $this->dir . '"> <![endif]-->',

            '<!--[if IE 8]><html class="no-js lt-ie9 ie8 oldie" '
            . 'lang="' . $this->lang . '" dir="' . $this->dir . '"> <![endif]-->',

            '<!--[if gt IE 8]><!--><html class="no-js" xmlns="http://www.w3.org/1999/xhtml" '
            . 'lang="' . $this->lang . '" dir="' . $this->dir . '" '
            . 'prefix="og: http://ogp.me/ns#" '
            // . 'prefix="ya: http://webmaster.yandex.ru/vocabularies/" '
            . '> <!--<![endif]-->',
        );

        return implode(" \n", $html) . "\n";
    }

    /**
     * Manual head render
     * @return string
     */
    public function renderHead()
    {
        $document = $this->doc;
        if (method_exists($document, 'getHeadData')) {
            $docData = $document->getHeadData();
        } else {
            return null;
        }

        $html = array();

        $isHtml5 = method_exists($this->doc, 'isHtml5') && $this->doc->isHtml5();

        // Generate charset when using HTML5 (should happen first)
        if ($isHtml5) {
            $html[] = '<meta charset="' . $document->getCharset() . '" />';
        }

        // Generate base tag (need to happen early)
        $base = $document->getBase();
        if (!empty($base)) {
            $html[] = '<base href="' . $document->getBase() . '" />';
        }

        // Generate META tags (needs to happen as early as possible in the head)
        foreach ($docData['metaTags'] as $type => $tag) {
            foreach ($tag as $name => $content) {
                if ($type == 'http-equiv' && !($isHtml5 && $name == 'content-type')) {
                    $html[] = '<meta http-equiv="' . $name . '" content="' . htmlspecialchars($content) . '" />';
                } elseif ($type == 'standard' && !empty($content)) {
                    $html[] = '<meta name="' . $name . '" content="' . htmlspecialchars($content) . '" />';
                }
            }
        }

        if ($docData['description']) {
            $html[] = '<meta name="description" content="' . htmlspecialchars($docData['description']) . '" />';
        }

        if ($generator = $document->getGenerator()) {
            $html[] = '<meta name="generator" content="' . htmlspecialchars($generator) . '" />';
        }

        $html[] = '<title>' . htmlspecialchars($docData['title'], ENT_COMPAT, 'UTF-8') . '</title>';

        // Generate stylesheet links
        foreach ($docData['styleSheets'] as $strSrc => $strAttr) {
            $tag = '<link rel="stylesheet" href="' . $strSrc . '"';

            if (!is_null($strAttr['mime']) && (!$isHtml5 || $strAttr['mime'] != 'text/css')) {
                $tag .= ' type="' . $strAttr['mime'] . '"';
            }

            if (!is_null($strAttr['media'])) {
                $tag .= ' media="' . $strAttr['media'] . '"';
            }

            if ($temp = JArrayHelper::toString($strAttr['attribs'])) {
                $tag .= ' ' . $temp;
            }

            $tag .= ' />';

            $html[] = $tag;
        }

        // Generate script file links
        foreach ($docData['scripts'] as $strSrc => $strAttr) {
            $tag = '<script src="' . $strSrc . '"';

            $defaultMimes = array('text/javascript', 'application/javascript', 'text/x-javascript', 'application/x-javascript');

            if (!is_null($strAttr['mime']) && (!$isHtml5 || !in_array($strAttr['mime'], $defaultMimes))) {
                $tag .= ' type="' . $strAttr['mime'] . '"';
            }

            if ($strAttr['defer']) {
                $tag .= ' defer="defer"';
            }

            if ($strAttr['async']) {
                $tag .= ' async="async"';
            }

            $tag .= '></script>';
            $html[] = $tag;
        }

        // add custom
        foreach ($docData['custom'] as $custom) {
            $html[] = $custom;
        }

        return implode("\n  ", $html) . "\n\n";
    }

    /**
     * @param string|array $metaRows
     * @return $this
     */
    public function meta($metaRows)
    {
        if (is_array($metaRows)) {
            foreach ($metaRows as $metaRow) {
                $this->meta($metaRow);
            }
        } else {
            if (method_exists($this->doc, 'getHeadData')) {
                $data = $this->doc->getHeadData();
                if (!in_array($metaRows, $data['custom'])) {
                    $this->doc->addCustomTag($metaRows);
                }
            }
        }

        return $this;
    }

    /**
     * Get relative template path (for browser)
     * @return string
     */
    protected function _getTemplatePath()
    {
        $path = pathinfo($this->_getTemplatePathFull(), PATHINFO_BASENAME);
        return rtrim($this->baseurl, '/') . '/templates/' . $path;
    }

    /**
     * Check is path external
     * @param string $path
     * @return int
     */
    protected function _isExternal($path)
    {
        $regs = array('http:\/\/', 'https:\/\/', '\/\/');
        $reg = '#^(' . implode('|', $regs) . ')#iu';

        return preg_match($reg, $path);
    }

    /**
     * @param $filename
     * @return mixed|string
     */
    protected function _getExtension($filename)
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (strpos($ext, '?')) {
            $ext = preg_replace('#(\?.*)$#', '', $ext);
        }

        return $ext;
    }

    /**
     * Get absolute template path (filesystem)
     * @return string
     */
    protected function _getTemplatePathFull()
    {
        $path = rtrim(realpath(__DIR__ . '/../../'), '/'); // TODO Remove template path hack
        //$path = JPath::clean(JPATH_THEMES . '/jblank'); // hardcode fix
        //$path = JPath::clean(JPATH_THEMES . '/' . $this->doc->template); // bug in Joomla on Error page
        return $path;
    }

    /**
     * Get site language
     * @return string
     */
    protected function _getLangDefault()
    {
        $lang = explode('-', JFactory::getLanguage()->getDefault());
        return $lang[0];
    }

    /**
     * Get current site language
     * @return string
     */
    protected function _getLangCurrent()
    {
        $lang = explode('-', JFactory::getLanguage()->getTag());
        return $lang[0];
    }

    /**
     * Get vars from request
     * @return stdClass
     */
    protected function _getRequest()
    {
        $data = array(
            'option' => $this->request('option'),
            'view' => $this->request('view'),
            'layout' => $this->request('layout'),
            'tmpl' => $this->request('tmpl', 'index'),
            'lang' => $this->request('lang', $this->langDef),
            'Itemid' => $this->request('Itemid', 0, 'int'),
        );

        if (class_exists('Joomla\Registry\Registry')) {
            $request = new Joomla\Registry\Registry();
            $request->loadArray($data);

        } else if (class_exists('JRegistry')) { // is depricated since J!3
            $request = new JRegistry();
            $request->loadArray($data);

        } else {
            $request = (object)$data;
        }

        return $request;
    }

    /**
     * Check file exists and return last modified
     * @param $path
     * @return int|null
     */
    protected function _checkFile($path)
    {
        $path = JPath::clean($path);
        if (JFile::exists($path) && filesize($path) > 5) {
            $mdate = substr(filemtime($path), -3);
            return $mdate;
        }

        return null;
    }

    /**
     * @return string
     */
    protected function _getBaseUrl()
    {
        if (0) { // experimental
            $root = JUri::root();
            $juri = new JUri($root);
            return '//' . $juri->toString(array('host', 'port', 'path'));
        }

        return JUri::root();
    }

    /**
     * @return JBlankMobileDetect
     */
    protected function _getMobile()
    {
        return new JBlankMobileDetect();
    }

    /**
     * Set new generator in meta
     * @param string|null $newGenerator
     * @return $this
     */
    public function generator($newGenerator = null)
    {
        $this->doc->setGenerator($newGenerator);
        return $this;
    }

    /**
     * Set html5 mode
     * @param bool $state
     * @return $this
     */
    public function html5($state)
    {
        if (method_exists($this->doc, 'setHtml5')) {
            $this->doc->setHtml5((bool)$state);
        }

        return $this;
    }

    /**
     * @param array $patterns
     * @return $this
     */
    public function excludeCSS(array $patterns)
    {
        $this->_excludeAssets(array('styleSheets' => $patterns));
        return $this;
    }

    /**
     * @param array $patterns
     * @return $this
     */
    public function excludeJS(array $patterns)
    {
        $this->_excludeAssets(array('scripts' => $patterns));
        return $this;
    }

    /**
     * Cleanup system links from Joomla, Zoo, JBZoo
     * @param array $allPatterns
     * @return $this
     */
    protected function _excludeAssets(array $allPatterns)
    {
        if (method_exists($this->doc, 'getHeadData')) {
            $data = $this->doc->getHeadData();
        } else {
            return $this;
        }

        foreach ($allPatterns as $type => $patterns) {
            foreach ($data[$type] as $path => $meta) {

                foreach ($patterns as $pattern) {
                    if (preg_match('#' . $pattern . '#iu', $path)) {
                        unset($data[$type][$path]);
                        break;
                    }
                }

                $this->setHeadData($type, $data);
            }
        }

        return $this;
    }

    /**
     * Are there any errors on this page?
     * @return bool
     */
    public function isError()
    {
        $buffer = $this->doc->getBuffer('message');

        if (is_array($buffer)) {
            $bufferWords = JString::trim(strip_tags(current($buffer['message'])));
        } else {
            $bufferWords = JString::trim(strip_tags($buffer));
        }

        return !empty($bufferWords);
    }

    /**
     * Check is current device is mobile
     * @return bool
     */
    public function isMobile()
    {
        return $this->mobile->isMobile() && !$this->mobile->isTablet();
    }

    /**
     * Check is current device is tablet
     * @return bool
     */
    public function isTablet()
    {
        return $this->mobile->isTablet();
    }

    /**
     * Check is current device is iOS
     * @return bool
     */
    public function isiOS()
    {
        return $this->mobile->isiOS();
    }

    /**
     * Check is current device is Android OS
     * @return bool
     */
    public function isAndroidOS()
    {
        return $this->mobile->isAndroidOS();
    }

    /**
     * Attention! Function chanage template contect.
     * It means that $this will be instance of JBlankTemplate
     *
     * @param $name
     * @param array $args
     * @return string
     */
    public function partial($name, array $args = array())
    {
        $file = preg_replace('/[^A-Z0-9_\.-]/i', '', $name);

        $ext = pathinfo($file, PATHINFO_EXTENSION);
        if (empty($ext)) {
            $file .= '.php';
        }

        $args['tpl'] = $this;
        $args['_this'] = $this->doc;

        // load the partial
        $__file = JPath::clean($this->partial . '/' . $file);
        $__fileTheme = JPath::clean($this->partialTheme . '/' . $file);

        extract($args);
        ob_start();
        if (JFile::exists($__file)) include($__file);
        if (JFile::exists($__fileTheme))  include($__fileTheme);
        $output = ob_get_contents();
        ob_end_clean();
        return $output;
    }

    /**
     * Simple checking type of current page
     * @return bool
     */
    public function isFront()
    {
        $defId = $this->menu->getDefault()->id;
        $curId = 0;

        $active = $this->menu->getActive();
        if ($active && $active->id) {
            $curId = $active->id;
        }

        return $defId == $curId;
    }

    /**
     * Enable or disable debug mode
     * @param bool $state
     * @return $this
     */
    public function debug($state = true)
    {
        $this->_debugMode = (bool)$state;
        return $this;
    }

    /**
     * Merging all css or js files (that already have been included via Joomla API)
     *     USE IT ON YOUR OWN RISK!!!
     * @param string $type
     * @param bool $isCompress
     * @return $this
     */
    public function merge($type = 'css', $isCompress = false)
    {
        $mergeFiles = array();

        $dataKey = $type == 'css' ? 'styleSheets' : 'scripts';

        if (method_exists($this->doc, 'getHeadData')) {
            $docData = $this->doc->getHeadData();
        }

        if (isset($docData) && !empty($docData[$dataKey])) {
            foreach ($docData[$dataKey] as $pathOrig => $attrs) {

                // don't get external files
                $path = str_replace($this->baseurl, '', $pathOrig);
                $path = preg_replace('#(\?.*)$#', '', $path);
                if ($this->_isExternal($path)) {
                    continue;
                }

                if (
                    // only media="all" and media=NULL
                    ($attrs['mime'] == 'text/css' && (!isset($attrs['media']) || strtolower($attrs['media']) == 'all'))
                    // any JavaScript
                    || ($attrs['mime'] == 'text/javascript')
                ) {
                    $fullPath = JPath::clean(JPATH_ROOT . '/' . $path);
                    $fullPathFolder = JPath::clean($_SERVER['DOCUMENT_ROOT'] . '/' . $path);
                    $resPath = false;

                    if (JFile::exists($fullPath)) {
                        $resPath = $fullPath;
                    } else if (JFile::exists($fullPathFolder)) {
                        $resPath = $fullPathFolder;
                    }

                    if ($resPath) {
                        $mergeFiles[] = $resPath;
                        unset($docData[$dataKey][$pathOrig]);
                    }

                }
            }
        }

        if (count($mergeFiles)) {
            $processor = JBlankMinify::getProcessor($type, $this);
            if ($path = $processor->minify($mergeFiles, $isCompress)) {
                $this->setHeadData($dataKey, $docData);
                if ('css' == $type) {
                    $this->doc->addStylesheet($path, 'text/css');
                } else if ('js' == $type) {
                    $this->doc->addScript($path, "text/javascript", false, false);
                }
            }
        }

        return $this;
    }

    /**
     * Set head data
     * Hack for empty scripts or styles arrays
     * @param string $type
     * @param array $data
     */
    protected function setHeadData($type, $data)
    {
        if (!empty($data[$type])) {
            $this->doc->setHeadData($data);

        } else if ($type == 'scripts') {
            $this->doc->_scripts = array();

        } else if ($type == 'styleSheets') {
            $this->doc->_styleSheets = array();
        }
    }

    //Получение материалов из категории
    public function getMaterialsOfCategory($catId = '', $select = '*')
    {
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select($select);
        $query->from('#__content');
        $query->where('catid="' . $catId . '" AND state="1"');
        $query->order('ordering ASC');

        $db->setQuery((string)$query);
        $res = $db->loadObjectList();

        return $res;
    }

    //Получить url страницы
    public function getUrlPage($artid = false, $arr = false, $is_category = false)
    {
        // если категория либо публикация
        if ($is_category) {
            return JRoute::_(ContentHelperRoute::getCategoryRoute($artid));
        } else {


            $uri = JURI::root();
            $lang = JComponentHelper::getParams('com_languages')->get('site');

            // define database object and query
            $db = JFactory::getDBO();
            $query = $db->getQuery(true);

            // build query and put the result into an array
            $query->select($db->quoteName(array('a.id', 'a.alias', 'a.language', 'b.id', 'b.alias'), array('idart', 'aliasart', 'languageart', 'idcat', 'aliascat')));
            $query->from($db->quoteName('#__content', 'a'));
            $query->join('LEFT', $db->quoteName('#__categories', 'b') . ' ON (' . $db->quoteName('a.catid') . ' = ' . $db->quoteName('b.id') . ')');
            $query->where($db->quoteName('a.id') . " = " . $db->quote($artid));

            // submit query
            $db->setQuery($query);

            // put the result into an associative array
            $resultasc = $db->loadAssoc();

            if (!$resultasc) return "";


            // write canonical url tag into an articel with langauge "All"
            if ($resultasc['languageart'] == '*') {
                if ($arr) return array($resultasc['aliascat'], $resultasc['aliasart']);
                return $uri . substr($lang, 0, 2) . '/' . $resultasc['aliascat'] . '/' . $resultasc['aliasart'];
                //return $uri . substr($lang, 0, 2) . '/' . $resultasc['idcat'] . '-' . $resultasc['aliascat'] . '/' . $resultasc['idart'] . '-' . $resultasc['aliasart'];

                // write canonical url tag into an articel with a specific language
            } else {
                $resultasc['aliascat'] = strtr($resultasc['aliascat'], array("-ua" => "", "-ru" => "", "-en" => ""));
                if ($arr) return array($resultasc['aliascat'], $resultasc['aliasart']);
                return $uri . substr($resultasc['languageart'], 0, 2) . '/' . $resultasc['aliascat'] . '/' . $resultasc['aliasart'];
                //return $uri.substr($resultasc['languageart'],0,2).'/'.$resultasc['idcat'].'-'.$resultasc['aliascat'].'/'.$resultasc['idart'].'-'.$resultasc['aliasart'];
            }


            if ($id) {
                $article = JControllerLegacy::getInstance('Content')
                    ->getModel('Article')->getItem($id);

                $url = JRoute::_(ContentHelperRoute::getArticleRoute($id,
                    $article->catid,
                    $article->language));

                return $url;
            }
        }
        return false;
    }


    //Определение оптимальной обрезки и масштаба изображений
    public function cropAndResizeImage($image = '', $w_o = false, $h_o = false, $pos = "top")
    {
        $image = strtr($image, array("http://" => "", $_SERVER['SERVER_NAME'] => ""));
        $w_o = $w_o ? $w_o : 300;
        $h_o = $h_o ? $h_o : 200;

        if (!$image) {
            $app =& JFactory::getApplication();
            $image = "/templates/" . $app->getTemplate() . "/images/no-photo.png";
        }

        if (file_exists(JPATH_ROOT . $image)) {
            $name = md5($image . filemtime($image) . $w_o . $h_o);
            if (file_exists(JPATH_ROOT . "/images/thumbnails/" . $name . ".jpg")) {
            } else {
                $this->resize(JPATH_ROOT . $image, $w_o, $h_o, JPATH_ROOT . "/images/thumbnails/" . $name . ".jpg", 90);
                $this->crop(JPATH_ROOT . "/images/thumbnails/" . $name . ".jpg", false, ($pos == "top") ? 1 : false, $w_o, $h_o);
            }
            return "/images/thumbnails/" . $name . ".jpg";
        }
        return "";
    }

    //Обрезка изображения
    public function crop($image, $x_o, $y_o, $w_o, $h_o)
    {
//        if (($x_o < 0) || ($y_o < 0) || ($w_o < 0) || ($h_o < 0)) {
//            echo "Некорректные входные параметры";
//            return false;
//        }
        list($w_i, $h_i, $type) = getimagesize($image); // Получаем размеры и тип изображения (число)

        $x_o = $x_o ? $x_o : round(($w_i - $w_o) / 2);
        $y_o = $y_o ? $y_o : round(($h_i - $h_o) / 2);

        $types = array("", "gif", "jpeg", "png"); // Массив с типами изображений
        $ext = $types[$type]; // Зная "числовой" тип изображения, узнаём название типа
        if ($ext) {
            $func = 'imagecreatefrom' . $ext; // Получаем название функции, соответствующую типу, для создания изображения
            $img_i = $func($image); // Создаём дескриптор для работы с исходным изображением
        } else {
            echo 'Некорректное изображение'; // Выводим ошибку, если формат изображения недопустимый
            return false;
        }
        if ($x_o + $w_o > $w_i) $w_o = $w_i - $x_o; // Если ширина выходного изображения больше исходного (с учётом x_o), то уменьшаем её
        if ($y_o + $h_o > $h_i) $h_o = $h_i - $y_o; // Если высота выходного изображения больше исходного (с учётом y_o), то уменьшаем её
        $img_o = imagecreatetruecolor($w_o, $h_o); // Создаём дескриптор для выходного изображения
        imagecopy($img_o, $img_i, 0, 0, $x_o, $y_o, $w_o, $h_o); // Переносим часть изображения из исходного в выходное
        $func = 'image' . $ext; // Получаем функция для сохранения результата
        return $func($img_o, $image, 90); // Сохраняем изображение в тот же файл, что и исходное, возвращая результат этой операции
    }

    //Изменение размеров изображения
    public function resize($img, $w, $h, $newfilename, $quality = 90)
    {
        //Check if GD extension is loaded
        if (!extension_loaded('gd') && !extension_loaded('gd2')) {
            trigger_error("GD is not loaded", E_USER_WARNING);
            return false;
        }

        //Get Image size info
        $imgInfo = getimagesize($img);

        if (isset($imgInfo[0]) && isset($imgInfo[1]))
            if ($imgInfo[0] && $imgInfo[1]) {
                $w = $w ? $w : (round($h * $imgInfo[0] / $imgInfo[1]));
                $h = $h ? $h : (round($w * $imgInfo[1] / $imgInfo[0]));
            }

        switch ($imgInfo[2]) {
            case 1:
                $im = imagecreatefromgif($img);
                break;
            case 2:
                $im = imagecreatefromjpeg($img);
                break;
            case 3:
                $im = imagecreatefrompng($img);
                break;
            default:
                trigger_error('Unsupported filetype!', E_USER_WARNING);
                break;
        }

        //yeah, resize it, but keep it proportional
        if ($w / $imgInfo[0] > $h / $imgInfo[1]) {
            $nWidth = $w;
            $nHeight = $imgInfo[1] * ($w / $imgInfo[0]);
        } else {
            $nWidth = $imgInfo[0] * ($h / $imgInfo[1]);
            $nHeight = $h;
        }

        $nWidth = round($nWidth);
        $nHeight = round($nHeight);

        $newImg = imagecreatetruecolor($nWidth, $nHeight);

        /* Check if this image is PNG or GIF, then set if Transparent*/
        if (($imgInfo[2] == 1) OR ($imgInfo[2] == 3)) {
            imagealphablending($newImg, false);
            imagesavealpha($newImg, true);
            $transparent = imagecolorallocatealpha($newImg, 255, 255, 255, 127);
            imagefilledrectangle($newImg, 0, 0, $nWidth, $nHeight, $transparent);
        }
        imagecopyresampled($newImg, $im, 0, 0, 0, 0, $nWidth, $nHeight, $imgInfo[0], $imgInfo[1]);

        if (($imgInfo[2] == 1) or ($imgInfo[2] == 2) or ($imgInfo[2] == 3)) imagejpeg($newImg, $newfilename, $quality);
        else trigger_error('Failed resize image!', E_USER_WARNING);
        return $newfilename;
    }

    public function string_cut($string, $length = 50, $etc = '...', $break_words = false, $middle = false)
    {
        if ($length == 0)
            return '';

        if (mb_strlen($string, 'UTF-8') > $length) {
            $length -= mb_strlen($etc, 'UTF-8');
            if (!$break_words && !$middle) {
                $string = preg_replace('/\s+?(\S+)?$/', '', mb_substr($string, 0, $length + 1, 'UTF-8'));
            }
            if (!$middle) {
                return mb_substr($string, 0, $length, 'UTF-8') . $etc;
            } else {
                return mb_substr($string, 0, $length / 2, 'UTF-8') . $etc . mb_substr($string, -$length / 2, 'UTF-8');
            }
        } else {
            return $string;
        }
    }

    //Применение шаблонов
    public function ApplyTemplatesIB($htmlPage, $tpl, $includes)
    {
        $listTemplates = array();
        $handle = opendir($tpl->partial);
        while (false !== ($getFileTemplate = readdir($handle))) {
            if (strpos($getFileTemplate, ".php") !== false) $listTemplates[] = strtr($getFileTemplate, array(".php" => ""));
        }
        closedir($handle);

        $handle = opendir($tpl->partialTheme);
        while (false !== ($getFileTemplate = readdir($handle))) {
            if (strpos($getFileTemplate, ".php") !== false) $listTemplates[] = strtr($getFileTemplate, array(".php" => ""));
        }
        closedir($handle);


        //Повторить применение шаблонов два (nhb) раза, для случаев, когда в шаблоне прописан еще один шаблон
        $listTemplates = array_merge($listTemplates, $listTemplates, $listTemplates);

        foreach ($listTemplates as $templateItem) {
            $owerflowTemplate = 0;


            $posTemplateS = strpos($htmlPage, "[" . $templateItem . "]");
            $posTemplateS = ($posTemplateS === false) ? strpos($htmlPage, "[" . $templateItem . " ") : $posTemplateS;
            $posTemplateE = strpos($htmlPage, "[/" . $templateItem . "]", $posTemplateS);

            while (
                (false !== $posTemplateS) &&
                (false !== $posTemplateE)
            ) {

                $owerflowTemplate++;
                if ($owerflowTemplate > 1000) break;
                $templateString = substr($htmlPage, $posTemplateS, $posTemplateE + strlen("[/" . $templateItem . "]") - $posTemplateS);
                $templateString = strtr($templateString, array("[" . $templateItem . "]" => "<div>", "[" . $templateItem . " " => "<div ", "[/" . $templateItem . "]" => "</div>"));

                if (substr($templateString, 0, 5) !== "<div>")
                    $templateString = substr_replace($templateString, ">", strpos($templateString, "]"), 1);

                $templateString = str_get_html($templateString);
                $templateString = $templateString->find('div', 0);

                $paramsTemplate = isset($templateString->attr) ? $templateString->attr : array();
                $paramsTemplate['content'] = $templateString->innertext;

                foreach ($includes as $field => $value) {
                    $paramsTemplate[$field] = $value;
                }

                $htmlPage = substr_replace(
                    $htmlPage,
                    $tpl->partial($templateItem, $paramsTemplate),
                    $posTemplateS,
                    $posTemplateE + strlen("[/" . $templateItem . "]") - $posTemplateS
                );

                $posTemplateS = strpos($htmlPage, "[" . $templateItem . "]");
                $posTemplateS = ($posTemplateS === false) ? strpos($htmlPage, "[" . $templateItem . " ") : $posTemplateS;
                $posTemplateE = strpos($htmlPage, "[/" . $templateItem . "]", $posTemplateS);
            }
        }
        return $htmlPage;
    }

    public function priceFormat($price, $format = true)
    {
        $price = $price ? $price : 0;
        $price = $price / 100;
        $price = number_format($price, 2, '.', ' ');
        $price = str_replace(' ', $format ? '&nbsp;' : ' ', $price);
        $price = str_replace('.', '.' . ($format ? '<small class="cents">' : ''), $price);
        if ($format) $price .= "</small>";

        return $price;
    }

    //Обновление сессии (по свойству SESS)
    public function SESSION()
    {
        $session = \JFactory::getSession();
        $session->set('IBModule', serialize($GLOBALS['IB'] -> SESS));
        return;
    }

    public function langText($lang)
    {
        $langTextArray = array();
        switch ($lang) {
            case 'ru':
                $langTextArray = array(
                    'weekdays' => ['0', 'января', 'февраля', 'марта', 'апреля', 'мая', 'июня', 'июля', 'августа', 'сентября', 'октября', 'ноября', 'декабря'],
                    'allnews' => 'Все новости',
                    'read_more' => 'Читать далее',
                    'more_news' => 'Просмотреть больше',
                );

                break;
            case 'uk':
                $langTextArray = array(
                    'weekdays' => ['0', 'січня', 'лютого', 'березня', 'квітня', 'травня', 'червня', 'липня', 'серпня', 'вересня', 'жовтня', 'листопада', 'грудня'],
                    'allnews' => 'Всі новини',
                    'read_more' => 'Читати більше',
                    'more_news' => 'Переглянути більше',
                );
                break;
        }
        return $langTextArray;
    }

    // получить массив со всеми отделениями
    public function getBranches($select = '*')
    {
        //$branches = array();
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);
        $query->select($select);

        $query->from('#__branches_'.TEMPLATE);
        //$query->where('catid="'.$catId.'" AND state="1"');
        //$query->leftJoin('ordering ASC');

        $db->setQuery((string)$query);
        $result = $db->loadObjectList();

        return $result;
    }

    //Получить список городов (отделений)
    public function getCitiesOfBranches() {
        $db = JFactory::getDBO();
        $q = "SELECT c.city_id as city_id, c.title_ru as city_ru, c.title_ua as city_ua, c.title_en as city_en, r.region_id as region_id, r.title_ru as region_ru, r.title_ua as region_ua, r.title_en as region_en FROM `_cities` as c JOIN `_regions` as r ON c.region_id=r.region_id WHERE city_id IN (SELECT city FROM `#__branches_industrial`)";
        $db->setQuery($q);
        $data_rows_assoc_list = $db->loadAssocList();

        $lang = $this->lang;
        $lang = strtr($lang, array("uk" => "ua"));

        $result = array();
        foreach ($data_rows_assoc_list as $item) {
            $result[$item['region_id']]['region'] = $item['region_'.$lang];
            $result[$item['region_id']]['cities'][$item['city_id']] = $item['city_'.$lang];

        }

        return json_encode($result, JSON_UNESCAPED_UNICODE);
    }

    //Получить id текущей страницы
    public function getPageId(){
        $id = false;
        $type = false;
        if (JRequest::getVar('view') == 'category') {
            $option   = JRequest::getCmd('option');
            $view   = JRequest::getCmd('view');
            $temp   = JRequest::getString('id');
            $temp   = explode(':', $temp);
            if ($option == 'com_content' && $view == 'category' && $temp[0]) {
                $id = $temp[0]; //this is category ID
                $type = "category";
            }
        } else if (JRequest::getVar('view') == 'article') {
            $id = JRequest::getInt('id');
            $type = "article";
        }
        return array($id, $type);
    }

    public function dateStr($date = false, $lang = 'ru'){
        $date=explode(".", date("d.m.Y", $date));
        if ($lang == "ru") {
            switch ($date[1]){
                case 1: $m='января'; break;
                case 2: $m='февраля'; break;
                case 3: $m='марта'; break;
                case 4: $m='апреля'; break;
                case 5: $m='мая'; break;
                case 6: $m='июня'; break;
                case 7: $m='июля'; break;
                case 8: $m='августа'; break;
                case 9: $m='сентября'; break;
                case 10: $m='октября'; break;
                case 11: $m='ноября'; break;
                case 12: $m='декабря'; break;
            }
        } elseif (($lang == "ua") or ($lang == "uk")){
            switch ($date[1]){
                case 1:  $m='січня'; break;
                case 2:  $m='лютого'; break;
                case 3:  $m='март'; break;
                case 4:  $m='апрель'; break;
                case 5:  $m='травні'; break;
                case 6:  $m='червня'; break;
                case 7:  $m='липні'; break;
                case 8:  $m='серпня'; break;
                case 9:  $m='вересня'; break;
                case 10: $m='жовтня'; break;
                case 11: $m='листопада'; break;
                case 12: $m='грудня'; break;
            }
        }
        return $date[0].'&nbsp;'.$m.'&nbsp;'.$date[2];
    }


    public function num2str($num, $lang = 'ru') {
        if ($lang == "ru") {
            $nul='ноль';
            $ten=array(
                array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
                array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
            );
            $a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
            $tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
            $hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
            $unit=array( // Units
                array('копейка' ,'копейки' ,'копеек',	 1),
                array('гривна'   ,'	гривны'   ,'гривен'    ,0),
                array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
                array('миллион' ,'миллиона','миллионов' ,0),
                array('миллиард','милиарда','миллиардов',0),
            );
        } elseif (($lang == "ua") or ($lang == "uk")){
            $nul='нуль';
            $ten=array(
                array('',"один", "два", "три", "чотири", "п'ять", "шість", "сім", "вісім", "дев'ять"),
                array('',"одна", "дві", "три", "чотири", "п'ять", "шість", "сім", "вісім", "дев'ять"),
            );
            $a20=array("десять", "одинадцять", "дванадцять", "тринадцять", "чотирнадцять", "п'ятнадцять", "шістнадцять", "сімнадцять", "вісімнадцять", "дев'ятнадцять");
            $tens=array(2=>"двадцять", "тридцять", "сорок", "п'ятдесят", "шістдесят", "сімдесят", "вісімдесят", "дев'яносто");
            $hundred=array("","сто", "двісті", "триста", "чотириста", "п'ятсот", "шістсот", "сімсот", "вісімсот", "дев'ятсот");
            $unit=array( // Units
                array("копійка", "копійки", "копійок",	 1),
                array("гривня", "гривні", "гривень"    ,0),
                array("тисяча", "тисячі", "тисяч"     ,1),
                array("мільйон", "мільйони", "мільйонів" ,0),
                array("мільярд", "міліарда", "мільярдів",0)
            );
        }
        //
        list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
        $out = array();
        if (intval($rub)>0) {
            foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
                if (!intval($v)) continue;
                $uk = sizeof($unit)-$uk-1; // unit key
                $gender = $unit[$uk][3];
                list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
                else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
                // units without rub & kop
                if ($uk>1) $out[]= $this -> morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
            } //foreach
        }
        else $out[] = $nul;
        $out[] = $this -> morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
        $out[] = $kop.' '.$this -> morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
        return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
    }

    /**
     * Склоняем словоформу
     * @ author runcore
     */
    public function morph($n, $f1, $f2, $f5) {
        $n = abs(intval($n)) % 100;
        if ($n>10 && $n<20) return $f5;
        $n = $n % 10;
        if ($n>1 && $n<5) return $f2;
        if ($n==1) return $f1;
        return $f5;
    }

    public function getAllParentsOfCurrentPage(){
        $menu = JFactory::getApplication()->getMenu();
        $getActive = $menu->getActive();
        return $getActive -> tree;
    }

    //Авторизация по TOKEN, переданного в POST
    public function AuthorizationAPI() {
        if (isset($_POST['TOKEN'])) {
            $_POST['TOKEN'] = trim(str_replace(array("\r","\n"),"",$_POST['TOKEN']));
            $GLOBALS['IB']->SESS['TOKEN'] = $_POST['TOKEN'];
            $GLOBALS['IB']->TOKEN = $_POST['TOKEN'];
            $GLOBALS['IB']->EXCEPTION = false;
            $GLOBALS['IB'] -> SESSION();

            if (isset($_POST['redirect'])) {
                header("Location: ".$_POST['redirect']);
                exit();
            }
        }
    }

    //Обработка AJAX запроса с указанным шаблоном
    public function processAjaxTemplate() {
        if (isset($_GET['AJAX'])) {
            $TEMPLATE = $_GET['TEMPLATE'];
            $htmlPage = "[$TEMPLATE][/$TEMPLATE]";
            $htmlPage = $this -> ApplyTemplatesIB($htmlPage, $this, array("IB" => $GLOBALS['IB'], "I18N" => I18N));
            $htmlPage = strtr($htmlPage, I18N);
            echo $htmlPage;
            exit();
        }
    }

    public function priceColor($amount){
        if($amount > 0){
            return 'status-success';
        }
        elseif ($amount < 0){
            return 'status-fail';
        }else{
            return 'status-default';
        }
    }
}
