<?php
namespace tutorial\php;
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once( $_SERVER['DOCUMENT_ROOT']."/standalone.php" );

require_once 'Thrift/ClassLoader/ThriftClassLoader.php';

use Thrift\ClassLoader\ThriftClassLoader;

$GEN_DIR = realpath(dirname(__FILE__).'/..').'/php/gen-php';

$loader = new ThriftClassLoader();
$loader->registerNamespace('Thrift', __DIR__ . '/');
$loader->registerDefinition('shared', $GEN_DIR);
$loader->registerDefinition('tutorial', $GEN_DIR);
$loader->register();

$dir = __DIR__."/gen-php/";

$catalog = opendir($dir);
while ($filename = readdir($catalog )) {
    if (($filename == ".") or ($filename == "..")) continue;
    $filename = $dir.$filename;
    include_once($filename);
}
closedir($catalog);

use Thrift\Protocol\TJSONProtocol;
use Thrift\Transport\TCurlClient;
use Thrift\Transport\TSocket;
use Thrift\Transport\THttpClient;
use Thrift\Transport\TBufferedTransport;
use Thrift\Exception\TException;


class IBClass {
    public $TOKEN = '';
    public $SESS = [];
    public $EXCEPTION;
    public $COUNTER = 0;
    public $CONSTANTS = array();

    function __construct() {
        $this -> CONFIG = \JFactory::getConfig();

        //Includes classes
        //require_once "classes/remittances/class.php";

        $listClasses = get_declared_classes();
        $listClasses = array_flip($listClasses);
        foreach ($listClasses as $getClass=>$item) {
            if (strpos($getClass,"ServiceClient") !== false) {
                $classMethods = get_class_methods($getClass);
                if ($classMethods) {
                    $listClasses[$getClass] = array_flip($classMethods);
                    if (isset($listClasses[$getClass]['__construct'])) unset($listClasses[$getClass]['__construct']);
                }
            } else {
                unset($listClasses[$getClass]);
            }
        }
        $this -> SERVICES = $listClasses;

        //ADDRESSES ===============================================================================
        $this -> CONSTANTS['ADDR'] = array(
            "SERVER-ADDR-OperationsServiceClient" => "/ibank/thrift/operations-json",
            "SERVER-ADDR-FinancialInformationServiceClient" => "/ibank/thrift/financial-information-json",
            "SERVER-ADDR-TagServiceClient" => "/ibank/thrift/tag-json",
            "SERVER-ADDR-ContractsServiceClient" => "/ibank/thrift/contracts-json",
            "SERVER-ADDR-ContractOperationHistoryServiceClient" => "/ibank/thrift/operation-history-json",
            "SERVER-ADDR-UserRegistrationServiceClient" => "/ibank/thrift/register-json",
            "SERVER-ADDR-AuthServiceClient" => "/ibank/thrift/auth-json",
            "SERVER-ADDR-FeedBackServiceClient" => "/ibank/thrift/send-feed-back-json",
            "SERVER-ADDR-CardDigitilizationServiceClient" => ""
        );


        //CONSTANTS ===============================================================================
        $this -> CONSTANTS['FinancialOperationType'] = array(
            1=>'CONTRACT_TO_CONTRACT',
            2=>'SEP_TRANSFER',
            3=>'BILLER_PAYMENT',
            //    4=>'SWIFT_TRANSFER',
            //    5=>'P2P_IBANK',
            //    6=>'CASH_TRANSFER',
            //    7=>'MAKE_DEPOSIT',
            8=>'INTRABANK_TRANSFER',
            9=>'CARD_TO_CARD',
            10=>'CARD_TO_CONTRACT'
        );

        $this -> CONSTANTS['UserOperationStatus'] = array(
            0=>'SUCCESS',
            1=>'FAIL',
            2=>'CREATED',
            3=>'WAIT_FOR_PROCESSING'
        );

        $this -> CONSTANTS['ReportFormatType'] = array(
            1=>'HTML',
            2=>'PDF',
//            3=>'IMAGE',
//            4=>'CSV'
        );

        $this -> CONSTANTS['ContractType'] = array(
            1 => 'CREDIT',
            2 => 'ACCOUNT',
            3 => 'DEPOSIT',
            4 => 'CARD',
            5 => 'PREPAID'
        );

        $this -> CONSTANTS['ParameterType'] = array(
            1 => 'STRING',
            2 => 'NUMERIC',
            3 => 'DATE',
            4 => 'DATETIME',
            5 => 'PHONE',
            6 => 'CARDNUM'
        );

        //Методы, хранящиеся в сессии
        $this -> SERVICES_SESS = array(
            'getCurrencyExchangeRateList',
//            'getAuthSession',
//            'findAllCardContracts',
//            'findAllCreditContracts',
//            'findAllAccountContracts',
//            'findAllDepositContracts',
//            'fetchFinancialOperations',
//            'fetchCreditOperationHistoryItemsWithFromToDate',
//            'fetchAccountOperationHistoryItemsWithFromToDate',
//            'fetchDepositOperationHistoryItemsWithFromToDate',
//            'fetchCardContractOperationHistoryItemsWithFromToDate'
        );

//        $app = \JFactory::getApplication('site');
        $session = \JFactory::getSession();
        $getSess = $session->get( 'IBModule', false );
        $this -> SESS = $getSess ? unserialize($getSess) : array();

        $this -> SESS['SERVICES_SESS'] = isset($this -> SESS['SERVICES_SESS']) ? $this -> SESS['SERVICES_SESS'] : array();

        $this -> SESS['REFER'] = isset($this -> SESS['REFER']) ? $this -> SESS['REFER'] : "$_SERVER[REQUEST_URI]";

        foreach ($this -> SERVICES_SESS as $service) {
            foreach ($this -> SESS['SERVICES_SESS'] as $service_ => $item) {
                if (strpos($service_, $service."_") === 0) continue;
                unset($this -> SESS['SERVICES_SESS'][$service_]);
            }
        }

        $this -> SESS['TIME'] = isset($this -> SESS['TIME']) ? $this -> SESS['TIME'] : time();

        if (isset($this -> SESS['TOKEN'])) $this -> TOKEN = $this -> SESS['TOKEN'];
    }

    public function request($method = '', $args) {
        $debug_backtrace = debug_backtrace();

        if (!$method) return false;
        $key = $method."_".md5(serialize($args));

        if (isset($this -> SESS['SERVICES_SESS'][$key])) {
            return $this -> SESS['SERVICES_SESS'][$key];
        }

        foreach ($this -> SERVICES as $Service => $item) {
            if (isset($item[$method])) {
                $url = $this -> CONFIG -> get('SERVER_URL');
                $port = $this -> CONFIG -> get('SERVER_PORT');
                $addr = $this -> CONSTANTS['ADDR']['SERVER-ADDR-'.$Service];
                $status = false;
                try {
                    $this -> log("**".$method."**: start | ".((isset($debug_backtrace[0]['file']) && isset($debug_backtrace[0]['line'])) ? ($debug_backtrace[0]['file'].":".$debug_backtrace[0]['line']) : '')." |".var_export($args, true).PHP_EOL.PHP_EOL);
                    $socket = new TCurlClient($url, $port, $addr, ($port == 443 ? "https" : "http"));
                    $socket -> setTimeoutSecs(3000);

                    $transport = new TBufferedTransport($socket, 1024, 1024);

                    $protocol = new TJSONProtocol($transport);

                    $ServiceName = '\\'.$Service;

                    if (strpos($ServiceName, "OperationsService") !== false) {
//                        $this -> SESS['SERVICES_SESS'] = array();
//                        $this -> SESSION();
                    }

                    $client = new $ServiceName($protocol);
                    $transport->open();

                    $result = call_user_func_array(array($client, $method), $args);

                    $transport->close();


                    $this -> SESS['SERVICES_SESS'][$key] = $result;
                    $this -> SESSION();


                    $serResult = var_export($result, true);
                    $serResult = (strlen($serResult) < 5000) ? $serResult : "...";

                    $this -> log($method.": success | ".$serResult.PHP_EOL.PHP_EOL);
                    $status = true;
                } catch (TException $tx) {
                    $this -> EXCEPTION = "";

                    $this -> log($method.": error");
                    if (isset($tx->errorMessageKey)) if($tx->errorMessageKey == 'deny_operation') return false;
                    $this -> EXCEPTION = $tx;
                }

                if ($status) return $result;

                break;
            }
        }

        return false;
    }

    //Получить все счета пользователя
    public function getAllAccounts($ContractType = array(1,2,3,4,5)) {
        $result = array();

        $ContractType = !count($ContractType) ?  [1,2,3,4,5] : $ContractType;

        //CREDITS
        if (in_array(1, $ContractType)) {
            $AllCredit = $this -> request("findAllCreditContracts", [
                $this->TOKEN,
                new \Attributes()
            ]);
            if ($AllCredit)
                foreach ($AllCredit as $credit) {
                    $result[] = array(
                        "id" => $credit -> id,
                        "mainAccountNumber" => $credit -> mainAccountNumber,
                        "title" => isset($credit -> productTitle) ? ($credit -> productTitle ? $credit -> productTitle : $credit -> productTitle) : $credit -> mainAccountNumber,
                        "currency" => $credit -> mainAccountCurrency,
                        "type" => 1,
                        "balance" => $credit -> balance,
                        "object" => $credit
                    );
                }
        }

        //ACCOUNTS
        if (in_array(2, $ContractType)) {
            $AllAccount = $this -> request("findAllAccountContracts", [
                $this->TOKEN,
                new \Attributes()
            ]);
            if ($AllAccount)
                foreach ($AllAccount as $acount) {
                    $result[] = array(
                        "id" => $acount -> id,
                        "mainAccountNumber" => $acount -> mainAccountNumber,
                        "title" => isset($acount -> productTitle) ? ($acount -> productTitle ? $acount -> productTitle : $acount -> productTitle) : $acount -> mainAccountNumber,
                        "currency" => $acount -> mainAccountCurrency,
                        "type" => 2,
                        "balance" => $acount -> balance,
                        "object" => $acount
                    );
                }
        }

        //DEPOSITS
        if (in_array(3, $ContractType)) {
            $AllDeposit = $this -> request("findAllDepositContracts", [
                $this->TOKEN,
                new \Attributes()
            ]);
            if ($AllDeposit)
                foreach ($AllDeposit as $deposit) {
                    $result[] = array(
                        "id" => $deposit -> id,
                        "mainAccountNumber" => $deposit -> mainAccountNumber,
                        "title" => isset($deposit -> productTitle) ? ($deposit -> productTitle ? $deposit -> productTitle : $deposit -> productTitle) : $deposit -> mainAccountNumber,
                        "currency" => $deposit -> mainAccountCurrency,
                        "type" => 3,
                        "balance" => $deposit -> balance,
                        "object" => $deposit
                    );
                }
        }


        //CARDS
        if (in_array(4, $ContractType)) {
            $AllCard = $this -> request("findAllCardContracts", [
                $this->TOKEN,
                new \Attributes()
            ]);
            if ($AllCard)
                //print_r($AllCard);
                foreach ($AllCard as $card) {
                    //$balance = $card -> creditLimit  ? ($card -> creditLimit - $card -> usedCreditLimit) : $card->balance;
                    $balance = isset($card->balance) ? (int)$card->balance : 0;
                    $balance = $balance + (isset($card -> creditLimit) ? (int)$card -> creditLimit : 0);

                    if (isset($card -> cards))
                        if (is_array($card -> cards) && count($card -> cards)) {
                            foreach ($card -> cards as $index => $cardItem) {
                                $result[] = array(
                                    "id" => $card -> id,
                                    "mainAccountNumber" => $card -> mainAccountNumber,
                                    "title" => isset($cardItem -> cardNumberMask) ? ($cardItem -> cardNumberMask ? $cardItem -> cardNumberMask : $card -> legalNumber) : $card -> legalNumber,
                                    "currency" => $card -> mainAccountCurrency,
                                    "type" => 4,
                                    "card" => $cardItem,
                                    "cardId" => $cardItem -> id,
                                    "balance" => $balance,
                                    "available" => $balance,
                                    "object" => $card
                                );
                            }
                        }
                }
        }

        return $result;
    }

    //Получить все счета пользователя
    public function getFinanceOperations($account = false, $type = false, $data = []) {
        $currentTime = time();
        $currentDate = date("d.m.Y", $currentTime);

        $dateMinusMonth = date("d.m.Y", strtotime("-1 month", strtotime($currentDate)));
        $currentDateUnix = strtotime($currentDate);
        $data['from'] = isset($data['from']) ? preg_replace("/[^.0-9]/", '', $data['from']) : "";
        $data['to'] = isset($data['to']) ? preg_replace("/[^.0-9]/", '', $data['to']) : "";
        $dateFrom = isset($data['from']) ? ($data['from'] ? (strtotime($data['from']) ?  $data['from'] : $dateMinusMonth) : $dateMinusMonth) : $dateMinusMonth;
        $dateTo = isset($data['to']) ? ($data['to'] ? (strtotime($data['to']) ?  $data['to'] : $currentDate) : $currentDate) : $currentDate;
        $dateFrom = (strtotime($dateFrom) > $currentTime) ? $currentDate : $dateFrom;
        $dateFrom = (strtotime($dateFrom) > strtotime($dateTo)) ? $dateTo : $dateFrom;
        $dateFromUnix = strtotime($dateFrom);

        $dateToUnix = strtotime($dateTo)+3600*24;
        $modeFilter = !isset($data['mode']) ? "last" : ($data['mode'] ? $data['mode'] : "last");
        $lastFilter = !isset($data['last']) ? "7" : ($data['last'] ? $data['last'] : "7");

        $operationHistoryFilter = new \operationHistoryFilter();
        $getAllAccounts = $this -> getAllAccounts();

        if ($account){

            $historyFinance = array();
            $getAllAccountsType = $this -> getAllAccounts($type ? array($type) : array());
            foreach ($getAllAccountsType as $value) {

                if (isset($type) && $type && isset($value['type']) && $value['type']) {
                    if (((int) $type) !== ((int) $value['type'])) continue;
                }

                if (isset($value['type'])) {
                    switch ($value['type']){
                        case 4:
                            if (isset($_GET['card']) && ($_GET['card'] == $value['cardId'])) {
                                $Attributes = new \Attributes();
                                $attrsList = array();

                                $attrsList['cardId'] = $value['card']->id;
                                $attrsList['cardNumberMask'] = $value['card']->cardNumberMask;
                                $Attributes -> attrs = $attrsList;

                                $historyFinanceCard = $this -> request("fetchCardContractOperationHistoryItemsWithFromToDate", [
                                    $this->TOKEN,
                                    $value['id'],
                                    $operationHistoryFilter,
                                    ($modeFilter == "last") ? ($currentDateUnix - ($lastFilter * 3600 * 24)) * 1000 : ($dateFromUnix * 1000),
                                    ($modeFilter == "last") ? ($currentDateUnix + 3600*24) * 1000 : ($dateToUnix * 1000),
                                    $Attributes
                                ]);

                                foreach ($historyFinanceCard as $index => $item)
                                    $historyFinanceCard[$index] = array("operationDate" => $item -> operationDate, "id"=>md5($value['id']), "item" => $item);

                                $historyFinance = array_merge($historyFinance, $historyFinanceCard);
                            }
                            break;

                        case 2:
                            $historyFinanceAccount = $this -> request("fetchAccountOperationHistoryItemsWithFromToDate", [
                                $this->TOKEN,
                                $value['id'],
                                $operationHistoryFilter,
                                ($modeFilter == "last") ? ($currentDateUnix - ($lastFilter * 3600 * 24)) * 1000 : ($dateFromUnix * 1000),
                                ($modeFilter == "last") ? ($currentDateUnix + 3600*24) * 1000 : ($dateToUnix * 1000),
                                new \Attributes()
                            ]);

                            foreach ($historyFinanceAccount as $index => $item)
                                $historyFinanceAccount[$index] = array("operationDate" => $item -> operationDate, "id"=>md5($value['id']), "item" => $item);

                            $historyFinance = array_merge($historyFinance, $historyFinanceAccount);
                            break;

                        case 1:
                            $historyFinanceCredit = $this -> request("fetchCreditOperationHistoryItemsWithFromToDate", [
                                $this->TOKEN,
                                $value['id'],
                                $operationHistoryFilter,
                                ($modeFilter == "last") ? ($currentDateUnix - ($lastFilter * 3600 * 24)) * 1000 : ($dateFromUnix * 1000),
                                ($modeFilter == "last") ? ($currentDateUnix + 3600*24) * 1000 : ($dateToUnix * 1000),
                                new \Attributes()
                            ]);

                            foreach ($historyFinanceCredit as $index => $item)
                                $historyFinanceCredit[$index] = array("operationDate" => $item -> operationDate, "id"=>md5($value['id']), "item" => $item);

                            $historyFinance = array_merge($historyFinance, $historyFinanceCredit);
                            break;

                        case 3:
                            $historyFinanceDeposit = $this -> request("fetchDepositOperationHistoryItemsWithFromToDate", [
                                $this->TOKEN,
                                $value['id'],
                                $operationHistoryFilter,
                                ($modeFilter == "last") ? ($currentDateUnix - ($lastFilter * 3600 * 24)) * 1000 : ($dateFromUnix * 1000),
                                ($modeFilter == "last") ? ($currentDateUnix + 3600*24) * 1000 : ($dateToUnix * 1000),
                                new \Attributes()
                            ]);

                            foreach ($historyFinanceDeposit as $index => $item)
                                $historyFinanceDeposit[$index] = array("operationDate" => $item -> operationDate, "id"=>md5($value['id']), "item" => $item);

                            $historyFinance = array_merge($historyFinance, $historyFinanceDeposit);
                            break;
                    }
                }
            }

        } else {

            $historyFinance = array();
            $fetchFinancialOperations = $this -> request("fetchFinancialOperations", [
                $this->TOKEN,
                $operationHistoryFilter,
                ($modeFilter == "last") ? ($currentDateUnix - ($lastFilter * 3600 * 24)) * 1000 : ($dateFromUnix * 1000),
                ($modeFilter == "last") ? ($currentDateUnix + 3600*24) * 1000 : ($dateToUnix * 1000),
                new \Attributes()
            ]);
            if($fetchFinancialOperations){
                foreach ($fetchFinancialOperations as $index => $operation) {
                    $historyFinance[] = array("operationDate" => $operation->createDate, "item" => $operation);
                }
            }
        }
        rsort($historyFinance);
        $historyFinanceArr = [];
        foreach ($historyFinance as $historyBlock) {
            $subjectContract = (isset($historyBlock['item']->dstContractRef))? $historyBlock['item']->dstContractRef->id : "";
            if(isset($historyBlock['item']->subject->subjectSEP->accountNumber)){
                $accountNumber = $historyBlock['item']->subject->subjectSEP->accountNumber;
            }
            // если нет номера счета получателя
            else{
                $templateID = isset($historyBlock['item']->templateId) ? $historyBlock['item']->templateId : "";
            }

            $operationID = isset($historyBlock['item']->id)? $historyBlock['item']->id : "";

            $historyItem = $historyBlock['item'];

            $amountInCents = isset($historyItem -> amountInCents) ? $historyItem -> amountInCents : "";

            if ($account){
                $historyDateTime = isset($historyItem -> operationDate) ? $historyItem -> operationDate/1000 : "";
                // Примечания к платежам родные
                $finOperationTitle = isset($historyItem -> description) ? $historyItem -> description : "";
            }
            else{
                $historyDateTime = isset($historyItem -> createDate) ? $historyItem -> createDate/1000 : "";

                // статические тексты назначения платежа
                if (!isset($this->CONSTANTS['FinancialOperationType'][$historyItem -> type])) continue;
                $finOperationTitle = '{CABINET-OPETATION-'.$this->CONSTANTS['FinancialOperationType'][$historyItem -> type].'}';
            }

            $historyDate = date("d.m.Y", $historyDateTime);
            $historyTime = date("H:i", $historyDateTime);

            $currency = isset($historyItem -> currency) ? $historyItem -> currency : "";
            $classColor = isset($historyItem -> status) ? strtolower("status-".$this->CONSTANTS['UserOperationStatus'][$historyItem -> status]) : "";

            $mainAccountNumber = "";

            foreach ($getAllAccounts as $value) {
                if (isset($historyItem->srcContractRef->id) && isset($historyItem->srcContractRef->type))
                    if (($historyItem->srcContractRef->id == $value['id']) && ($historyItem->srcContractRef->type == $value['type'])){
                        $mainAccountNumber = $value['mainAccountNumber'];
                    }
            }

            $historyFinanceArr_ = [
                'historyItem'      => $historyItem,
                'historyDate'       => $historyDate,
                'historyTime'       => $historyTime,
                'finOperationTitle' => $finOperationTitle,
                'classColor'        => $classColor,
                'amountInCents'     => $amountInCents,
                'currency'          => $currency,
                'operationID'       => $operationID,
                'mainAccountNumber' => $mainAccountNumber
            ];
            $historyFinanceArr[] = $historyFinanceArr_;
        }

        return array(
            "historyFinance" => $historyFinance,
            "historyFinanceArr" => $historyFinanceArr,
            "dateFrom" => $dateFrom,
            "dateTo" => $dateTo,
            "modeFilter" => $modeFilter,
            "lastFilter" => $lastFilter,
            "currentDateUnix" => $currentDateUnix,
            "dateFromUnix" => $dateFromUnix,
            "dateToUnix" => $dateToUnix
        );
    }

    public function getAllTemplates($limit = false) {
        $findAllOperationTemplates = $this -> request("findAllOperationTemplates", [
            $this->TOKEN
        ]);

        if (!$findAllOperationTemplates) return false;

        foreach ($findAllOperationTemplates as $index => $item) {
            if (isset($item -> origin)){
                if (
                    ($item -> origin == "0") or
                    (!isset($this->CONSTANTS['FinancialOperationType'][$item -> operationSubject -> type]))
                ) {
                    unset($findAllOperationTemplates[$index]);
                    continue;
                }
            }
            $findAllOperationTemplates[$index] = array("updateDate" => $item -> updateDate, "item" => $item);
        }
        arsort($findAllOperationTemplates);

        if ($limit) {
            $findAllOperationTemplates = array_slice($findAllOperationTemplates,0, $limit);
        }

        return $findAllOperationTemplates;
    }

    public function log($str){
        $log = file_get_contents($_SERVER['DOCUMENT_ROOT']."/templates/jblank/log.md");
        $log = $log ? explode(PHP_EOL,$log) : array();

        $log[] = "**".(date("d.m.Y H:i:s", time()))."** ".$str;
        $log = array_slice($log, count($log) - 1000);

        file_put_contents($_SERVER['DOCUMENT_ROOT']."/templates/jblank/log.md",implode(PHP_EOL,$log));
    }

    //Обновление сессии (по свойству SESS)
    public function SESSION()
    {
        $session = \JFactory::getSession();
        $session->set('IBModule', serialize($GLOBALS['IB']->SESS));
        return;
    }

    //Обработка исключений по умолчанию
    public function EXCEPTION_process($oops = true) {
        global $tpl;

        if ($this->EXCEPTION) {
            if (isset($this->EXCEPTION->errorMessageKey)) {
                $trans = "{EXCEPTION-" . strtoupper(strtr($this->EXCEPTION->errorMessageKey, array("_" => "-"))) . "}";

                if (isset(I18N[$trans])) {
                    $this->EXCEPTION = '';
                    return I18N[$trans];
                } else {
                    $EXCEPTION = $this -> EXCEPTION;

                    $erMessageKey = isset($EXCEPTION->errorMessageKey) ? $EXCEPTION->errorMessageKey : "";
                    $fileException = $erMessageKey.".php";

                    $pathFull = strtr($tpl -> pathFull, array("\\" => "/"));


                    if (isset($EXCEPTION->errorMessageKey) && file_exists($pathFull."/php/Exception/".$fileException)) {
                        require_once $pathFull."/php/Exception/".$fileException;
                    } else {
                        //require_once $pathFull."/php/Exception/default.php";
                        $EXCEPTION = $this->EXCEPTION;
                        $this->EXCEPTION = '';
                        if(isset($EXCEPTION->errorDescription)){
                            $EXCEPTION = $EXCEPTION->errorDescription;
                        }else{
                            if(isset($EXCEPTION->errorMessage)){
                                $EXCEPTION = $EXCEPTION->errorMessage;
                            }else{
                                $EXCEPTION = $EXCEPTION->errorMessageKey;
                            }
                        }
                        return $EXCEPTION;
                    }
                }
            } else {
                return "Server error";
            }
        }
        return false;
    }

    //Includes block, snippets...
    public function includes($item = '', $params = []) {
        global $tpl, $IB;
        $item = strtr($item, array(".php"=>"")).".php";

        extract($params);

        if (file_exists($tpl -> pathFull."/".$item)) {
            include $tpl -> pathFull."/".$item;
        } elseif (file_exists($tpl -> pathFullTemplate."/".$item)) {
            include $tpl -> pathFullTemplate."/".$item;
        } else {
            //echo "file not exist - ".$item;
        }
    }

    public function userRole($role){
        $result = $this->request('getAuthSession', array($this->TOKEN));
        $roles = array();
        foreach ($result->roles as $role_item){
            $roles[] = $role_item->code;
        }
        return in_array($role, $roles);
    }

    //Обработка исключений по умолчанию
    public function error_process($attrs = array()) {
        // не показывать пользователю ошибку otp_required, потому что сразу даем ввести код
        if (!empty($attrs['errorMessageKey']) && $attrs['errorMessageKey'] !== 'otp_required') {
            $trans = "{EXCEPTION-" . strtoupper(strtr($attrs['errorMessageKey'], array("_" => "-"))) . "}";
            if (isset(I18N[$trans])) {
                $ERROR = I18N[$trans];
            } else {
                if(isset($attrs['errorDescription'])){
                    $ERROR = $attrs['errorDescription'];
                }else{
                    if(isset($attrs['errorMessage'])){
                        $ERROR = $attrs['errorMessage'];
                    }else{
                        $ERROR = $attrs['errorMessageKey'];
                    }
                }
            }
            return $ERROR;
        }
        return false;
    }

}




