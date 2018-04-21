<?php
class JConfig {
    public function __construct(){
     //   if ("1" == "1") {
            $this->TEMPLATE = "dvbank";
            $this->user = 'ibank_web';
            $this->password = 'ibank_web';
            $this->db = 'ibank_web';
            $this->minimize = false;
            $this->stylesCache = false;

            $this-> SERVER_URL = "ibank-test.payforce.net.ua";
            $this-> SERVER_PORT = "443";
            $this-> URL_WEBSITE = "https://dvbank.vis-design.com";
            $this-> URL_WEBSITE_AUTH = "https://dvbank.vis-design.com/ibank-auth-redirect";
            $this-> URL_WEBSITE_FOREX = "https://dvbank.vis-design.com/forex";
            $this-> URL_WEBSITE_RADIO = "https://dvbank.vis-design.com/radio";
            $this-> URL_WEBSITE_SEARCH = "https://dvbank.vis-design.com/search";
            $this-> URL_FACEBOOK = "https://www.facebook.com";
            $this-> PHONE_NUMBER = "0 800 503 305";

            $this-> cabinet_remittance_CARD_TO_CONTRACT_param0 = [1,2,3,4];
            $this-> cabinet_remittance_CARD_TO_CONTRACT_param1 = [4];

            $this-> NEWCARD_URL_UA = "https://dvbank.vis-design.com/privatnim-kliyentam/platizhni-kartki";
            $this-> NEWCARD_URL_RU = "https://dvbank.vis-design.com/ru/privatnim-kliyentam/platizhni-kartki";
            $this-> NEWACCOUNT_URL_UA = "https://dvbank.vis-design.com/privatnim-kliyentam/potochni-rahunki-ta-platezhi";
            $this-> NEWACCOUNT_URL_RU = "https://dvbank.vis-design.com/ru/privatnim-kliyentam/potochni-rahunki-ta-platezhi";
            $this-> NEWDEPOSIT_URL_UA = "https://dvbank.vis-design.com/privatnim-kliyentam/depoziti";
            $this-> NEWDEPOSIT_URL_RU = "https://dvbank.vis-design.com/ru/privatnim-kliyentam/depoziti";
            $this-> NEWCREDIT_URL_RU = "https://dvbank.vis-design.com/ru/privatnim-kliyentam/krediti";
            $this-> NEWCREDIT_URL_UA = "https://dvbank.vis-design.com/ru/privatnim-kliyentam/krediti";

            $this-> IOS_APP = "https://itunes.apple.com/us/app/dvb-nk/id1227301301?mt=8";
            $this-> ANDROID_APP = "https://play.google.com/store/apps/details?id=ua.dvbank.android.dv24";
            $this-> CONDITIONS_URL = "https://www.dvbank.ua/fileadmin/download/2016/%D0%9F%D1%80%D0%B0%D0%B2%D0%B8%D0%BB%D0%B0%20%D0%BA%D0%BE%D0%BC%D0%BF%D0%BB%D0%B5%D0%BA%D1%81%D0%BD%D0%BE%D0%B3%D0%BE%20%D0%BE%D0%B1%D1%81%D0%BB%D1%83%D0%B6%D0%B8%D0%B2%D0%B0%D0%BD%D0%B8%D1%8F%20%D1%84%D0%B8%D0%B7%D0%B8%D1%87%D0%B5%D1%81%D0%BA%D0%B8%D1%85%20%D0%BB%D0%B8%D1%86%20%D0%B2%20%D0%9F%D0%90%D0%9E%20%D0%94%D0%98%D0%92%D0%98%20%D0%91%D0%90%D0%9D%D0%9A2.pdf";
            //$this-> BILLERS_ICONS = "http://app-test.dvbank.payforce.net.ua:8080/dvbank/biller/icon/";
            $this-> BILLERS_ICONS = "https://ibank-test.payforce.net.ua/bank/biller/icon/";
            //$this-> BILLERS_AUTODETECT_URL = "http://app-test.dvbank.payforce.net.ua:8080/dvbank/billers-autodetect.xml";
            $this-> BILLERS_AUTODETECT_URL = "http://ibank-test.payforce.net.ua:8880/bank/billers-autodetect.xml";

            $this-> CARDS_SKIN_DIR = 'https://ibank-test.payforce.net.ua/bank/card/skin/';
       // }
       //  else {
       //     echo "Edit configuration.php"; exit();
       // }

        $this-> CABINET_ENTRY_URL = "/cabinet/products";
        $this-> CABINET_CARDS_URL = "/cabinet/products/cards";
        $this-> CABINET_ACCOUNTS_URL = "/cabinet/products/accounts";
        $this-> CABINET_CREDITS_URL = "/cabinet/products/credits";
        $this-> CABINET_DEPOSITS_URL = "/cabinet/products/deposits";
        $this-> CABINET_REMITTANCE_URL = "/cabinet/remittance";
        $this-> CABINET_TEMPLATES_URL = "/cabinet/templates";
        $this-> CABINET_CARDS_LIMIT = "/cabinet/products/cards/limits";
        $this-> CABINET_PAYMENTS = "/cabinet/payments";
        $this-> CABINET_FEEDBACK_URL = "/cabinet/feedback";
        $this-> CABINET_PROFILE_URL = "/cabinet/profile";
        $this-> CABINET_OPERATIONS_URL = "/cabinet/operations";
        $this-> DEVELOPER_URL = "http://payforce.ua/";
    }

    public $offline = '0';
    public $offline_message = 'Сайт закрыт на техническое обслуживание.<br />Пожалуйста, зайдите позже.';
    public $display_offline_message = '1';
    public $offline_image = '';
    public $sitename = 'Bank';
    public $editor = 'codemirror';
    public $captcha = '0';
    public $list_limit = '20';
    public $access = '1';
    public $debug = '0';
    public $debug_lang = '0';
    public $dbtype = 'mysqli';
    public $host = 'localhost';
    public $dbprefix = 'pubor_';
    public $live_site = '';
    public $secret = 'cRInG0ZVn2OgjpbA';
    public $gzip = '0';
    public $error_reporting = 'default';
    public $helpurl = 'https://help.joomla.org/proxy?keyref=Help{major}{minor}:{keyref}&lang={langcode}';
    public $ftp_host = '';
    public $ftp_port = '';
    public $ftp_user = '';
    public $ftp_pass = '';
    public $ftp_root = '';
    public $ftp_enable = '0';
    public $offset = 'UTC';
    public $mailonline = '1';
    public $mailer = 'mail';
    public $mailfrom = 'igor.frolikov@u-charged.com';
    public $fromname = 'DvBank';
    public $sendmail = '/usr/sbin/sendmail';
    public $smtpauth = '0';
    public $smtpuser = '';
    public $smtppass = '';
    public $smtphost = 'localhost';
    public $smtpsecure = 'none';
    public $smtpport = '25';
    public $caching = '0';
    public $cache_handler = 'file';
    public $cachetime = '15';
    public $cache_platformprefix = '0';
    public $MetaDesc = '';
    public $MetaKeys = '';
    public $MetaTitle = '1';
    public $MetaAuthor = '1';
    public $MetaVersion = '0';
    public $robots = '';
    public $sef = '1';
    public $sef_rewrite = '1';
    public $sef_suffix = '0';
    public $unicodeslugs = '0';
    public $feed_limit = '10';
    public $feed_email = 'none';
    public $log_path = '/home/ibank/ibank-cominbank-web/html/administrator/logs';
    public $tmp_path = '/home/ibank/ibank-cominbank-web/html/tmp';
    public $lifetime = '15';
    public $session_handler = 'database';
    public $memcache_persist = '1';
    public $memcache_compress = '0';
    public $memcache_server_host = 'localhost';
    public $memcache_server_port = '11211';
    public $memcached_persist = '1';
    public $memcached_compress = '0';
    public $memcached_server_host = 'localhost';
    public $memcached_server_port = '11211';
    public $redis_persist = '1';
    public $redis_server_host = 'localhost';
    public $redis_server_port = '6379';
    public $redis_server_auth = '';
    public $redis_server_db = '0';
    public $proxy_enable = '0';
    public $proxy_host = '';
    public $proxy_port = '';
    public $proxy_user = '';
    public $proxy_pass = '';
    public $massmailoff = '0';
    public $MetaRights = '';
    public $sitename_pagetitles = '0';
    public $force_ssl = '0';
    public $session_memcache_server_host = 'localhost';
    public $session_memcache_server_port = '11211';
    public $session_memcached_server_host = 'localhost';
    public $session_memcached_server_port = '11211';
    public $frontediting = '1';
    public $cookie_domain = '';
    public $cookie_path = '';
    public $asset_id = '1';
}
