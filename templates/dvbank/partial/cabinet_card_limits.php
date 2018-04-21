<?php
if(!empty($_GET['account'])&& !empty($_GET['card'])){

    $contractId = $_GET['account'];
    $contractIdDecode = urldecode($contractId);
    $cardId = $_GET['card'];


    if (!empty($_GET['cardNumberMask']) && !empty($_GET['limits'])){
        $cardNumberMask = $_GET['cardNumberMask'];
        $limitsArray = $_GET['limits'];
        //print_r($limitsArray);
        if (!empty($limitsArray)){
            $limitObjects = array();
            foreach ($limitsArray as $limitItem){


                //if (!empty($limitItem['quantity'])) {

                    $limitObject = new \Limit;
                    foreach ($limitItem as $key => $limitItemValue) {

                        if ($key == 'amount') {
                            if ($limitItemValue == "") {
                                $limitObject->{$key} = -1;
                            } else {
                                $limitObject->{$key} = $limitItemValue * 100;
                            }


                        }
                        // TODO ucharged (check for empty string for other object properties)
                        elseif ($key == 'quantity'){
                            if($limitItemValue !== ''){
                                $limitObject->{$key} = $limitItemValue;
                            }
                        }
                        else{
                            $limitObject->{$key} = $limitItemValue;
                        }
                    }
                    $limitObjects[] = $limitObject;



                //}
            }


            $ContractReference = new \ContractReference;
            // change limits
            $Attributes = new \Attributes();
            $ContractReference->id = $contractIdDecode;
            $ContractReference->type = 4; // тип - карта


            $cardContract = $IB->request('changeLimits', [
                $IB->TOKEN,
                $ContractReference,
                $cardNumberMask,
                $limitObjects,
                $Attributes
            ]);



            $EXCEPTION = $IB -> EXCEPTION_process();
            $SUCCESS_MESSAGE = (!empty($cardContract)) ?(($cardContract->status == 0)?'{CABINET-CARD-LIMITS-SUCCESS}':''):'';

        }
    }


    $cardContract = $IB -> request('fetchCardContractDetails', [
        $IB->TOKEN,
        $contractIdDecode,
        new \Attributes()
    ]);

    if ($IB -> EXCEPTION){
        if (isset($IB -> EXCEPTION -> errorMessageKey)) {
            $trans = "{EXCEPTION-".strtoupper(strtr($IB -> EXCEPTION -> errorMessageKey, array("_"=>"-")))."}";
            if (isset($I18N[$trans])) {
                $EXCEPTION = $I18N[$trans];
            }
            else{
                $EXCEPTION = $IB -> EXCEPTION -> errorMessageKey;
            }
            $IB -> EXCEPTION = '';
        }
    }


    ?>

    <div class="card-limits-main">
        <?php
            if (!empty($EXCEPTION)){
                ?>
                <div class="error_action"><?=$EXCEPTION?></div>
                <?php
            }
            if (!empty($SUCCESS_MESSAGE)){
                ?>
                <div class="success_action"><?=$SUCCESS_MESSAGE?></div>
                <?php
            }
        ?>
            <?php


            if(!empty($cardContract)){

                foreach ($cardContract->cards as $card) {
                    if ($card->id == $cardId){
                        $limits = $card->limits;
                        $cardNumberMask = $card->cardNumberMask;
                    }
                }

                if(!empty($limits)) {

                    $mainAccountCurrency = $cardContract->mainAccountCurrency;
                    ?>

                    <form class="col-md-6 col-sm-10 col-xs-12 card-limits card-limits_uneditable validate" method="get">
                    <div class="title">
                        <div class="title-content">
                            <span>{CABINET-CARD-LIMITS-CHANGE}</span>

                            <div class="change-limits-button">
                                <i class="icons icons_pencil-limits limits-change"></i>
                                <button><i class="icons icons_check limits-change limits-change_uneditable"></i></button>
                            </div>
                        </div>

                    </div>

                    <?php
                    $getAuthSession = $IB -> request("getAuthSession", [
                        $IB->TOKEN
                    ]);
                    echo ($getAuthSession->level)? '' : '<input class="system_ext_auth_required required" type="hidden" value="" />';
                    ?>
                    <input type="hidden" name="account" value="<?=$contractId?>"/>
                    <input type="hidden" name="card" value="<?=$cardId?>"/>
                    <input type="hidden" name="cardNumberMask" value="<?=$cardNumberMask?>" />

                    <?php
                    //print_r($limits);
                    $i = 0;
                    foreach ($limits as $limit) {
                        // limit title

                        if ($limit->type == 5){
                            if (!empty($limit->otherType)){


                                // TODO ucharged (RU_ -> need fix)
                                $trans = "{LIMIT-TYPE-".strtoupper(strtr($limit->otherType, array("_"=>"-", "RU_"=>"")))."}";
                                if (isset($I18N[$trans])) {
                                    $limitTitle = $I18N[$trans];
                                }
                                else{
                                    $limitTitle = $limit->otherType;
                                }
                            }
                            else{
                                $limitTitle = '';
                            }

                        }
                        else{
                            $limitTitlesArray = [     '',    '{LIMIT-TYPE-TOTAL}', '{LIMIT-TYPE-ATM}', '{LIMIT-TYPE-POS}', '{LIMIT-TYPE-INTERNET}'];
                            $limitTitle = $limitTitlesArray[$limit->type];
                        }
                        // terms
                        if (property_exists($limit, 'termType')){
                            $limitTermsArray = ['{LIMIT-TERM-TYPE-INSTANT}', '{LIMIT-TERM-TYPE-DAY}', '{LIMIT-TERM-TYPE-MONTH}', '{LIMIT-TERM-TYPE-YEAR}', '{LIMIT-TERM-TYPE-WEEK}', '{LIMIT-TERM-TYPE-QUARTER}'];
                            $termType = $limitTermsArray[$limit->termType];
                            // $limitTitle .= ' - за '.$termType;
                            echo "<input type=\"hidden\" name=\"limits[$i][termType]\" value=\"$limit->termType\" />";
                        }
                        if (!empty($limit->termValue)){
                            if ($limit->termValue > 1){
                                $limitTitle .= ' ('.$limit->termValue.')';
                            }
                            echo "<input type=\"hidden\" name=\"limits[$i][termValue]\" value=\"$limit->termValue\" />";
                        }

                        ?>
                        <input type="hidden" name="limits[<?=$i?>][type]" value="<?=$limit->type?>" />
                        <?php
                        if (!empty($limit->otherType)){
                            echo "<input type=\"hidden\" name=\"limits[$i][otherType]\" value=\"$limit->otherType\" />";
                        }
                        ?>

                        <table class="limit-item">
                            <thead>
                            <tr>
                                <th colspan="4"><?=$limitTitle?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if (property_exists($limit,'amount')){
                                // Summ of limit
                                $totalAmount = ($limit->amount == -1) ? "" : (($limit->amount !== '')?($limit->amount / 100):'');
                            ?>
                            <tr>
                                <td>{CABINET-CARD-LIMITS-SUMM}</td>
                                <td>
                                    <input type="number" name="limits[<?=$i?>][amount]" min="0" class="limits-input" value="<?=$totalAmount?>" placeholder="Без обмежень">
                                </td>
                                <td>
                                    <?php //echo $mainAccountCurrency; ?>
                                    {UAH}
                                </td>
                            </tr>
                            <?php
                            }
                            if(property_exists($limit,'quantity') && !empty($limit->termType)){  // если задано свойство и если тип операции - не разовая
                                // Quantity of limit
                                $limitQuantity = isset($limit->quantity)?($limit->quantity):'';
                            ?>
                            <tr>
                                <td>{CABINET-CARD-LIMITS-AMOUNT}</td>
                                <td><input type="number" name="limits[<?=$i?>][quantity]" min="-1" class="limits-input"  value="<?=$limitQuantity?>" placeholder="Без обмежень"></td>
                            </tr>
                            <?php
                            }
                            ?>
                            </tbody>
                        </table>

                        <?php

                        $i++;
                    }
                    ?>

                    </form>
                        <?php

                } else { ?>
                    <div class="col-md-6 col-sm-10 col-xs-12 card-limits" style="padding-bottom: 0">
                        <div class="title">
                            <div class="title-content">
                                <span>{CABINET-CARD-LIMITS-EMPTY}</span>
                            </div>
                        </div>
                    </div>
                <?php }
            }
            ?>

        <div class="back">
            <img src="<?=$tpl -> pathTemplate?>/img/content/back-arrow.png"><a href="<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_CARDS_URL').'?account='.$contractId.'&card='.$cardId)?>">{CABINET-TEMPLATES-EDIT-TEMPLATE-GOBACK}</a>
        </div>
    </div>
    <?php
}
?>
