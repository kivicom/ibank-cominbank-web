<?php
$cats = isset($_GET['data']['cat']) ? $_GET['data']['cat'] : [];
$path = $_SERVER['REQUEST_URI'];
$path = (false !== ($posU = strpos($path, "?"))) ? substr($path, 0, $posU) : $path;
$data = isset($_POST['data']) ? $_POST['data'] : false;

$stage = isset($_POST['confirm']) ? "execute" : "preface";
$EXCEPTION = false;

$prefaceBillPaymentOperation = false;
$executeBillPaymentOperation = false;

$findBillerById = false;



if (isset($_GET['detection'])) {
    switch ($_GET['detection']) {
        case "mobile":
            $phone = isset($data['phone_number']) ? $data['phone_number'] : false;
            $amountInCents = isset($data['amountInCents']) ? $data['amountInCents'] : false;
            $provider = false;
            $id = false;

            if ($phone && $amountInCents) {
                $phone = "+".preg_replace('/\D/', '', $phone);
                $amountInCents = preg_replace('/\D/', '', $amountInCents);

                $xml = simplexml_load_file($IB -> CONFIG -> get('BILLERS_AUTODETECT_URL'));
                $detection = $xml -> detection;
                foreach ($detection -> biller as $biller) {
                    foreach ($biller -> match as $match) {
                        $regexp = (string) $match['regexp'];
                        if (preg_match($regexp, $phone)) {
                            $provider = (string) $biller['provider'];
                            $id = (string) $biller['id'];
                            break;
                        }
                    }
                    if (($provider !== false) && ($id !== false)) break;
                }
            }

            if (($provider !== false) && ($id !== false)) {
                $_GET['provider'] = $provider;
                $_GET['biller'] = $id;
            }
            break;
    }
}

if (isset($_GET['biller']) && isset($_GET['provider'])) {
    $findBillerById = $IB->request("findBillerById", [
        $IB->TOKEN,
        urldecode($_GET['provider']),
        urldecode($_GET['biller'])
    ]);

    $getAllAccounts = $IB -> getAllAccounts();

    if ($findBillerById) {
        if ($data) {
            $data['amountInCents'] = preg_replace('/\D/', '', $data['amountInCents']);
            $amountInCents = $data['amountInCents'];

            $BillerOperationSubject = new \BillerOperationSubject;
            $BillerOperationSubject->billerId = urldecode($_GET['biller']);
            $BillerOperationSubject->billerAccount = current($data);
            $BillerOperationSubject->providerId = urldecode($_GET['provider']);

            $Attributes = new \Attributes;
            $Attributes->attrs = $data;
            $BillerOperationSubject->parameters = $Attributes;

            $ContractReference = new \ContractReference;
            $ContractReference->id = $data['id'];
            $ContractReference->type = $data['type'];

            ${$stage."BillPaymentOperation"} = $IB->request($stage."BillPaymentOperation", [
                $IB->TOKEN,
                $BillerOperationSubject,
                $amountInCents,
                $ContractReference
            ]);

            $BillPaymentOperation = ${$stage."BillPaymentOperation"};

            if ($BillPaymentOperation) {
                $attrs = isset($BillPaymentOperation -> attributes -> attrs) ? $BillPaymentOperation -> attributes -> attrs : false;
                if ($attrs) foreach ($attrs as $field => $attr) {
                    if ($field == "amountInCents") $data[$field] = preg_replace('/\D/', '', $attr);
                    else $data[$field] = $attr;
                }
            }

            $EXCEPTION = $IB -> EXCEPTION_process();
        }

        ?>
        <div class="biller">
            <ul class="biller__nav">
                <li class="active"><a href="<?= $_SERVER['REQUEST_URI'] ?>">{CABINET-REMITTANCE-STEP} 1</a></li>
                <li class="<?=($prefaceBillPaymentOperation or $executeBillPaymentOperation) ? "active" : ""?>" onclick="return false;"><a href="#">{CABINET-BILLERS-STEP} 2</a></li>
                <li class="<?=$executeBillPaymentOperation ? "active" : ""?>" onclick="return false;"><a href="#">{CABINET-BILLERS-STEP} 3</a></li>
                <li><?= $findBillerById->title ?></li>
            </ul>

            <?php
            if ($EXCEPTION) {
                ?>
                <div class="cabinet_remittance_error">
                    <?= $EXCEPTION ?>
                </div>
                <?php
            }

            if ($stage == "preface") {
                $formId = "form_".uniqid();
                ?>
                <form id="<?=$formId?>" class="form-templates active validate" method="post" action="">

                    <?php if ($prefaceBillPaymentOperation) { ?>
                        <input type="hidden" name="confirm" value="" />

                        <div class="form-templates__title">
                            {CABINET-REMITTANCE-CARD_TO_CONTRACT-TITLE}
                        </div>
                    <?php } ?>

                    <div class="client-profile__content remittance">
                        <?php
                        foreach ($findBillerById->parametersConf as $parameter) {
                            $value = isset($data[$parameter->name]) ? $data[$parameter->name] : $parameter->hint;

                            switch ($parameter->type) {
                                //STRING
                                case 1:
                                    ?>
                                    <div class="client-input" data-type="<?=$parameter->type?>">
                                        <label class="col-md-5 col-sm-5 col-xs-12"><?= $parameter->title ?></label>
                                        <input class="required col-md-5 col-sm-5 col-xs-12"
                                               name="data[<?= $parameter->name ?>]"
                                            <?php if ($prefaceBillPaymentOperation) echo 'disabled="disabled"'; ?>
                                               type="text" placeholder="<?= $value ?>"
                                               value="">
                                        <div class="col-md-3"></div>
                                        <?= $prefaceBillPaymentOperation ? '<input type="hidden" name="data[' . $parameter->name . ']" value="' . $value . '" />' : '' ?>
                                    </div>
                                    <?php
                                    break;

                                //NUMERIC
                                case 2:
                                    ?>
                                    <div class="client-input" data-type="<?=$parameter->type?>">
                                        <label class="col-md-5 col-sm-5 col-xs-12"><?= $parameter->title ?></label>
                                        <input class="required col-md-5 col-sm-5 col-xs-12" data-mask='digits'
                                               name="data[<?= $parameter->name ?>]"
                                            <?php if ($prefaceBillPaymentOperation) echo 'disabled="disabled"'; ?>
                                               type="text" placeholder="<?= $value ?>"
                                               value="">
                                        <div class="col-md-3"></div>
                                        <?= $prefaceBillPaymentOperation ? '<input type="hidden" name="data[' . $parameter->name . ']" value="' . $value . '" />' : '' ?>
                                    </div>
                                    <?php
                                    break;

                                //DATE
                                case 3:
                                    ?>
                                    <div class="client-input" data-type="<?=$parameter->type?>">
                                        <label class="col-md-5 col-sm-5 col-xs-12"><?= $parameter->title ?></label>
                                        <input class="required col-md-5 col-sm-5 col-xs-12 datepicker"
                                               name="data[<?= $parameter->name ?>]"
                                            <?php if ($prefaceBillPaymentOperation) echo 'disabled="disabled"'; ?>
                                               type="text" placeholder="<?= $value ?>"
                                               value="">
                                        <div class="col-md-3"></div>
                                        <?= $prefaceBillPaymentOperation ? '<input type="hidden" name="data[' . $parameter->name . ']" value="' . $value . '" />' : '' ?>
                                    </div>
                                    <?php
                                    break;

                                //DATETIME
                                case 4:
                                    ?>
                                    <div class="client-input" data-type="<?=$parameter->type?>">
                                        <label class="col-md-5 col-sm-5 col-xs-12"><?= $parameter->title ?></label>
                                        <input class="required col-md-5 col-sm-5 col-xs-12 datepicker"
                                               name="data[<?= $parameter->name ?>]"
                                            <?php if ($prefaceBillPaymentOperation) echo 'disabled="disabled"'; ?>
                                               type="text" placeholder="<?= $value ?>"
                                               value="">
                                        <div class="col-md-3"></div>
                                        <?= $prefaceBillPaymentOperation ? '<input type="hidden" name="data[' . $parameter->name . ']" value="' . $value . '" />' : '' ?>
                                    </div>
                                    <?php
                                    break;

                                //PHONE
                                case 5:
                                    ?>

                                    <div class="client-input" data-type="<?=$parameter->type?>">
                                        <label class="col-md-5 col-sm-5 col-xs-12"><?= $parameter->title ?></label>
                                        <input class="required col-md-5 col-sm-5 col-xs-12" data-mask='phone'
                                               name="data[<?= $parameter->name ?>]"
                                            <?php if ($prefaceBillPaymentOperation) echo 'disabled="disabled"'; ?>
                                               type="text" placeholder="<?= $value ?>"
                                               value="">
                                        <div class="col-md-3"></div>
                                        <?= $prefaceBillPaymentOperation ? '<input type="hidden" name="data[' . $parameter->name . ']" value="+' . preg_replace('/\D/', '', $value) . '" />' : '' ?>
                                    </div>
                                    <?php
                                    break;

                                //CARDNUM
                                case 6:
                                    ?>
                                    <div class="client-input">
                                        <label class="col-md-5 col-sm-5 col-xs-12"><?= $parameter->title ?></label>
                                        <input class="required col-md-5 col-sm-5 col-xs-12" data-mask='card_number'
                                               name="data[<?= $parameter->name ?>]"
                                            <?php if ($prefaceBillPaymentOperation) echo 'disabled="disabled"'; ?>
                                               type="text" placeholder="<?= $value ?>"
                                               value="">
                                        <div class="col-md-3"></div>
                                        <?= $prefaceBillPaymentOperation ? '<input type="hidden" name="data[' . $parameter->name . ']" value="' . $value . '" />' : '' ?>
                                    </div>
                                    <?php
                                    break;
                            }
                        }

                        $IB->includes("blocks/remittance_form_accounts_list", [
                            'data' => $data,
                            'label' => '{BILLER-FROM-ACCOUNT}',
                            'ContractType' => [],
                            'disabled' => $prefaceBillPaymentOperation
                        ]);

                        $IB -> includes("blocks/remittance_form_amount", [
                            'data' => $data,
                            'parameter' => 'amountInCents',
                            'required' => true,
                            'disabled' => $prefaceBillPaymentOperation
                        ]);

                        if ($prefaceBillPaymentOperation) {
                            if (isset($prefaceBillPaymentOperation->operationConditions->extAuthRequired))
                                if ($prefaceBillPaymentOperation->operationConditions->extAuthRequired === true) {
                                    ?>
                                    <input class="system_ext_auth_required required" type="hidden" value=""/>
                                    <?php
                                }

                            if (isset($prefaceBillPaymentOperation->operationConditions->commission) && $prefaceBillPaymentOperation->operationConditions->commission) {
                                $IB->includes("blocks/remittance_form_amount", [
                                    'data' => [
                                        'commission' => isset($prefaceBillPaymentOperation->operationConditions->commission) ? $prefaceBillPaymentOperation->operationConditions->commission : 0,
                                        'currency' => $IntrabankTransferOperation->currency
                                    ],
                                    'parameter' => 'commission',
                                    'label' => '{CABINET-REMITTANCE-COMISSION}',
                                    'disabled' => true,
                                    'required' => false
                                ]);
                            }

                            if (isset($_GET['detection']) && ($_GET['detection'] == "mobile")) { ?>
                                <div class="client-save">
                                    <a class="client-save__link" href="#"
                                       onclick="system_template_modal(jQuery(this)); return false;"
                                       data-form="<?= $formId ?>"
                                       data-finance_type="<?= $FinancialOperationTypeActive ?>"
                                    >{CABINET-REMITTANCE-SAVE-TEMPLATE}</a>
                                </div>
                                <?php
                            }
                        }
                        ?>

                        <div class="back col-md-5 col-sm-5 col-xs-5">
                            <img src="<?=$tpl -> pathTemplate?>/img/content/back-arrow.png">
                            <a href="<?= $prefaceBillPaymentOperation ? $_SERVER['REQUEST_URI'] : $path ?>">{CABINET-RETURN-BUTTON}</a>
                        </div>

                        <div class="col-md-5 col-sm-5 col-xs-5">
                            <button class="btn-green" type="submit">{BILLER-CATEGORIES-BUTTON-NEXT}</button>
                        </div>
                    </div>
                </form>
                <?php
            } else {
                ?>
                <div class="cabinet-remittance__success">
                    <p>
                        <?php
                        if ($executeBillPaymentOperation) {
                            echo "{CABINET-REMITTANCE-PAYMENT-".$IB -> CONSTANTS['UserOperationStatus'][$executeBillPaymentOperation->status]."}";
                        } else {
                            echo "{CABINET-REMITTANCE-PAYMENT-FAIL}";
                        }
                        ?>

                        <br/>
                        <br/>
                    <div>
                        <a href="<?=$path?>"><button type="submit" class="btn-press">Ok</button></a>
                    </div>
                    </p>

                </div>
                <?php
            }
            ?>
        </div>
        <?php

    } else {
        $EXCEPTION = $IB->EXCEPTION_process();
        if ($EXCEPTION) {
            ?>
            <div class="cabinet_remittance_error"><?= $EXCEPTION ?></div>
            <?php
        }
    }
} else {
    $fetchBillerCategories = $IB->request("fetchBillerCategories", [
        $IB->TOKEN,
        null
    ]);

    if ($fetchBillerCategories && is_array($fetchBillerCategories) && count($fetchBillerCategories)) {
        ?>
        <div class="billers-content">
            <h1>{BILLER-CATEGORIES-TITLE}</h1>

            <ul class="billers_cat_list">
                <?php
                foreach ($fetchBillerCategories as $item) {
                    ?>
                    <li class="billers_cat_list__item <?= (in_array($item->id, $cats) ? "active" : "") ?>">
                        <a href="<?= $path ?>?data[cat][0]=<?= $item->id ?>">
                            <div class="billers_cat_list__img">
                                <?php
                                $iconRoot = strtr($_SERVER['DOCUMENT_ROOT']."/templates/jblank/images/icons/billers/", array("//"=>"/"));
                                $filename = file_exists($iconRoot . $item->id . ".svg") ? $tpl->img ."/icons/billers/". $item->id . ".svg" : $tpl->img . "/icons/billers/default.png";
                                ?>
                                <img src="<?= $filename ?>" alt="<?= $item->id ?>"/>
                            </div>
                            <div class="billers_cat_list__title"><?= $item->title ?></div>
                        </a>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>

        <?php
        if (is_array($cats) && count($cats)) {
            foreach ($cats as $cat) {
                $fetchBillerCategoryPath = $IB->request("fetchBillerCategoryPath", [
                    $IB->TOKEN,
                    $cat
                ]);

                if (is_array($fetchBillerCategories) && count($fetchBillerCategories)) {
                    $fetchBillerCategoryPath = end($fetchBillerCategoryPath);

                    $fetchBillerCategories1 = $IB->request("fetchBillerCategories", [
                        $IB->TOKEN,
                        $cat
                    ]);

                    if ($fetchBillerCategories1 && is_array($fetchBillerCategories1) && count($fetchBillerCategories1)) {
                        ?>
                        <div class="billers-content">
                            <h2><?= $fetchBillerCategoryPath->title ?></h2>

                            <ul class="billers_cat_list">
                                <?php
                                $URI = "data[cat][0]=" . $cats[0] . "&";

                                foreach ($fetchBillerCategories1 as $item) {
                                    ?>
                                    <li class="billers_cat_list__item <?= (in_array($item->id, $cats) ? "active" : "") ?>">
                                        <a href="<?= $path ?>?<?= $URI ?>data[cat][1]=<?= $item->id ?>">
                                            <div class="billers_cat_list__img">
                                                <?php
                                                $iconRoot = strtr($_SERVER['DOCUMENT_ROOT']."/templates/jblank/images/icons/billers/", array("//"=>"/"));
                                                $filename = file_exists($iconRoot . $item->id . ".svg") ? $tpl->img ."/icons/billers/". $item->id . ".svg" : $tpl->img . "/icons/billers/default.png";
                                                ?>
                                                <img src="<?= $filename ?>" alt="<?= $item->id ?>"/>
                                            </div>
                                            <div class="billers_cat_list__title"><?= $item->title ?></div>
                                        </a>
                                    </li>
                                    <?php
                                }
                                ?>
                            </ul>
                        </div>
                        <?php
                    }
                }
            }
        }

        $currentCat = (is_array($cats) && count($cats)) ? end($cats) : null;

        if ($currentCat !== null) {
            $findBillers = $IB->request("findBillers", [
                $IB->TOKEN,
                null,
                $currentCat,
                null,
                0,
                10000,
                null
            ]);

            if ($findBillers && is_array($findBillers) && count($findBillers)) {
                ?>
                <ul class="billers_list">
                    <?php
                    foreach ($findBillers as $biller) {
                        ?>
                        <li class="billers_list__item">
                            <a href="<?= $path . "?biller=" . urlencode($biller->id) . "&provider=" . urlencode($biller->providerId) ?>">
                                <div class="billers_list__img">
                                    <?php
                                    if ($biller->iconRef):
                                        ?>
                                        <img src="<?= $IB -> CONFIG -> get('BILLERS_ICONS') ?><?= $biller->iconRef . ".png" ?>" alt="<?= $biller -> iconRef ?>"/>
                                        <?php
                                    else:
                                        ?>
                                        <img src="<?= $tpl->img . "/icons/billers/default.png" ?>" alt="<?= $biller -> iconRef ?>"/>
                                        <?php
                                    endif;
                                    ?>
                                </div>
                                <div class="billers_list__title"><?= $biller->title ?></div>
                            </a>
                        </li>
                        <?php
                    }
                    ?>
                </ul>
                <?php
            }
        }
    }
}
?>