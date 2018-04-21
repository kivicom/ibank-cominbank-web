<?php
/* reg_form.php */


// SuretyRecords
$EXCEPTION = false;
$EXCEPTION_LIST = [
    'suretyrecords_error' => 'Неверный код (suretyrecords_error)'
];


if (isset($_POST['sms_code']) && isset($_POST['method'])) {
    $smsCode = (string)$_POST['sms_code'];
    $result = $IB -> request(
        "activateSuretyRecords",
        array(
            $IB->SESS['TOKEN'],
            $smsCode,
            new \Attributes(),
        ));

    if ($IB -> EXCEPTION){
        if (isset($IB -> EXCEPTION -> errorMessageKey)) {
            if (isset($EXCEPTION_LIST[$IB -> EXCEPTION -> errorMessageKey])) {
                $EXCEPTION = $EXCEPTION_LIST[$IB -> EXCEPTION -> errorMessageKey];
                $IB -> EXCEPTION = '';
            }
            else{
                $EXCEPTION = $IB -> EXCEPTION -> errorMessageKey;
                $IB -> EXCEPTION = '';
            }
        }
        if (empty($IB->SESS['sms_sent'])){
            $sms_sent = 0;
        }
        else{
            $sms_sent = $IB->SESS['sms_sent'];
        }
        $sms_sent++;
        if ($sms_sent > 3){
            header('Location: '.$IB -> CONFIG -> get('CABINET_ENTRY_URL'));
            exit();
        }else{
            $IB->SESS['sms_sent'] = $sms_sent;
            $tpl -> SESSION();
        }


    }
    if ($result) {
        $IB->SESS['TOKEN'] = isset($result -> token) ? $result -> token : '';
        $IB->TOKEN = $IB->SESS['TOKEN'];
        $IB->SESS['USER'] = isset($result -> userInfo) ? (array) $result -> userInfo : false;
        $IB->EXCEPTION = false;
        $tpl -> SESSION();

        header('Location: '.$IB -> CONFIG -> get('CABINET_ENTRY_URL'));
        exit();

    }

}

// SuretyRecords







if (isset($_POST['args']) && isset($_POST['method'])) {
    $args = $_POST['args'];
    if ($_POST['method'] == 'registration'){
        $UserRegistrationForm = new \UserRegistrationForm();

        $UserRegistrationForm -> identifierType = 1; // тип регистрации - по номеру телефона регистрациия
        $UserRegistrationForm -> login = isset($args['phone']) ? $args['phone'] : ""; // телефон является логином
        $UserRegistrationForm -> password = isset($args['password']) ? $args['password'] : "";
        $UserRegistrationForm -> firstName = isset($args['firstName']) ? $args['firstName'] : "";
        $UserRegistrationForm -> secondName = isset($args['secondName']) ? $args['secondName'] : "";
        $UserRegistrationForm -> middleName = isset($args['middleName']) ? $args['middleName'] : "";
        $UserRegistrationForm -> email = isset($args['email']) ? $args['email'] : "";
        $UserRegistrationForm -> phone = isset($args['phone']) ? $args['phone'] : "";

        $Attributes = new \Attributes();
        $IB->SESS = array();

        $session = \JFactory::getSession();
        $session->set( 'IBModule', '');
        $result = $IB -> request(
            "registerUser",
            array(
                $UserRegistrationForm,
                $Attributes
            )
        );

        if ($IB -> EXCEPTION){
            if (isset($IB -> EXCEPTION -> errorMessageKey)) {
                $trans = "{EXCEPTION-".strtoupper(strtr($IB -> EXCEPTION -> errorMessageKey, array("_"=>"-")))."}";
                if (isset($I18N[$trans])) {
                    $EXCEPTION = $I18N[$trans];
                    $IB -> EXCEPTION = '';
                }
            }
        }

        if ($result) {
            $IB->SESS['TOKEN'] = isset($result -> token) ? $result -> token : '';
            $IB->TOKEN = $IB->SESS['TOKEN'];
            $IB->SESS['USER'] = isset($result -> userInfo) ? (array) $result -> userInfo : false;
            $IB->EXCEPTION = false;
            $tpl -> SESSION();
        }



    }
}

if ($IB->TOKEN) {

// SuretyRecord
    //print_r($result);
    function exitSuretyRecords($exitText){
        echo "<div class=\"error_action\">$exitText</div>";
    }
    function hasRole($role, $result){
        $rolesSet = $result->roles;
        $roles = array();
        foreach ($rolesSet as $role_item){
            $roles[] = $role_item->code;
        }
        return in_array($role, $roles);
    }

    function SuretyRecord($IB){

        return $findSuretyRecordsForActivation = $IB -> request(
            "findSuretyRecordsForActivation",
            array(
                $IB->TOKEN,
                new \Attributes(),
            ));
    }

    function SuccessExit($IB, $tpl){
        global $EXCEPTION_LIST;

        $returnArray = array();
        $findSuretyRecordsForActivation = SuretyRecord($IB);
        if ($findSuretyRecordsForActivation){
            //print_r($findSuretyRecordsForActivation);
            foreach ($findSuretyRecordsForActivation as $suretyRecord){
                if ($suretyRecord->activationChannelType == 1){
                    // sending code via sms
                    $requestActivationOfSuretyRecords = $IB -> request(
                        "requestActivationOfSuretyRecords",
                        array(
                            $IB->TOKEN,
                            new \Attributes(),
                        ));

                    if ($IB -> EXCEPTION){
                        if (isset($IB -> EXCEPTION -> errorMessageKey)) {
                            if (isset($EXCEPTION_LIST[$IB -> EXCEPTION -> errorMessageKey])) {
                                $EXCEPTION = $EXCEPTION_LIST[$IB -> EXCEPTION -> errorMessageKey];
                                $IB -> EXCEPTION = '';
                            }
                        }
                    }
                    else{
                        $phoneMask = $suretyRecord->activatoinAddressMask;
                    }
                    break;
                }
            }
        }
        else{
            // enter into the cabinet
            header('Location: '.$IB -> CONFIG -> get('CABINET_ENTRY_URL'));
            exit();
        }
        return (!empty($phoneMask))? $phoneMask : FALSE;
    }

    //print_r($result);
    if (hasRole('guest',$result)){
        if (hasRole('bound_to_contragent', $result)){
            //вызов findSuretyRecordsForActivation

            $phoneMask = SuccessExit($IB, $tpl);
        }
        else{
            // вызов bindContragentToUser
            $ContragentToUserBindingForm = new \ContragentToUserBindingForm;
            $ContragentToUserBindingForm->identifierType = 1; // DEAL NUMBER
            $ContragentToUserBindingForm->identifier = (string)$result->userInfo->name;


            $bindContragentToUser = $IB -> request(
                "bindContragentToUser",
                array(
                    $IB->TOKEN,
                    $ContragentToUserBindingForm
                ));
            if ($bindContragentToUser){
                $result = $bindContragentToUser;
                $IB->SESS['TOKEN'] = isset($result -> token) ? $result -> token : '';
                $IB->TOKEN = $IB->SESS['TOKEN'];
                $IB->SESS['USER'] = isset($result -> userInfo) ? (array) $result -> userInfo : false;
                $IB->EXCEPTION = false;
                $tpl -> SESSION();

                if (hasRole('bound_to_contragent', $result)){
                    $phoneMask = SuccessExit($IB, $tpl);
                }
                else {
                    exitSuretyRecords('Не удалось привязать пользователя к контрагенту');
                }
            }
            else{
                // enter into the cabinet
                header('Location: '.$IB -> CONFIG -> get('CABINET_ENTRY_URL'));
                exit();
            }
        }
    }
    else{
        $phoneMask = SuccessExit($IB, $tpl);
    }

    // Surety record
//    header('Location: '.$IB -> CONFIG -> get('CABINET_ENTRY_URL'));
//    exit();

}
?>
<div class="registration col-md-12 col-sm-12 col-xs-12 cabinet-registration">



    <div class="registration col-md-6 col-sm-10 col-xs-12">
        <?php
        if ($EXCEPTION) {
            ?>
            <div class="cabinet_remittance_error">
                <?= $EXCEPTION ?>
            </div>
            <?php
        }


        if (!empty($phoneMask) || !empty($sms_sent)){
            ?>

            <div class="services">
                <div class="services__name form_header">
                    <span>На номер <?=$phoneMask?> {TEXT-SMS-SENT}</span>
                </div>
            </div>
            <form class="form-reg col-md-12 col-sm-12 col-xs-12 validate" action="" method="post">
                <input type="hidden" name="method" value="activateSuretyrecords" />
                <div class="reg-input col-md-12 col-sm-12 col-xs-12">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <label class="col-md-5 col-sm-5 col-xs-10" for="firstName">{SURETY-RECORD-SMS-CODE}:</label>
                        <input class="col-md-6 col-sm-6 col-xs-10 required" name="sms_code" id="sms_code" type="text" placeholder="" >
                    </div>
                </div>
                <button class="btn-press">{TEXT-SUBMIT}</button>
            </form>

            <?php
        }
                else{
                    if (empty($result)){
                        ?>

                        <form class="form-reg col-md-12 col-sm-12 col-xs-12 validate" action="" method="post"  onsubmit='jQuery("#phone_hidden").val(jQuery("#phone").val().replace(/[^0-9+]/g, ""))'>

                            <input type="hidden" name="method" value="registration" />
                            <div class="reg-input col-md-12 col-sm-12 col-xs-12">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <label class="col-md-5 col-sm-5 col-xs-10" for="firstName">{REG-FORM-FIRSTNAME-TITLE}:</label>
                                    <input class="col-md-6 col-sm-6 col-xs-10 required" name="args[firstName]" id="firstName" type="text" placeholder="" <?php echo isset($_POST['args']['firstName'])? 'value="'.$_POST['args']['firstName'].'"':''; ?> >
                                </div>
                            </div>
                            <div class="reg-input col-md-12 col-sm-12 col-xs-12">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <label class="col-md-5 col-sm-5 col-xs-10" for="lastName">{REG-FORM-LASTNAME-TITLE}:</label>
                                    <input class="col-md-6 col-sm-6 col-xs-10 required" name="args[lastName]" id="lastName" type="text" placeholder="" <?php echo isset($_POST['args']['lastName'])? 'value="'.$_POST['args']['lastName'].'"':''; ?> >
                                </div>
                            </div>
                            <div class="reg-input col-md-12 col-sm-12 col-xs-12">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <label class="col-md-5 col-sm-5 col-xs-10" for="phone">{REG-FORM-PHONE-TITLE}:</label>
                                    <input id="phone" class="col-md-6 col-sm-6 col-xs-10 required" id="phone" type="phone" placeholder="+38 (___) ___ __ __" data-mask="phone" <?php echo isset($_POST['args']['phone'])? 'value="'.$_POST['args']['phone'].'"':''; ?> >
                                    <input id="phone_hidden"  type="hidden" name="args[phone]" />
                                </div>
                            </div>
<!--                            <div class="reg-input col-md-12 col-sm-12 col-xs-12">-->
<!--                                <div class="col-md-12 col-sm-12 col-xs-12">-->
<!--                                    <label class="col-md-5 col-sm-5 col-xs-10" for="birthday">{REG-FORM-BIRTHDAY-TITLE}:</label>-->
<!--                                    <input class="col-md-6 col-sm-6 col-xs-10 required" name="args[birthday]" id="birthday" type="date" --><?php //echo isset($_POST['args']['birthday'])? 'value="'.$_POST['args']['birthday'].'"':''; ?><!-- >-->
<!--                                </div>-->
<!--                            </div>-->
                            <div class="reg-input col-md-12 col-sm-12 col-xs-12">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <label class="col-md-5 col-sm-5 col-xs-10" for="email">{REG-FORM-EMAIL-TITLE}:</label>
                                    <input class="col-md-6 col-sm-6 col-xs-10 required email" name="args[email]" id="email" type="email" placeholder="" <?php echo isset($_POST['args']['email'])? 'value="'.$_POST['args']['email'].'"':''; ?> >
                                </div>
                            </div>
                            <div class="reg-input col-md-12 col-sm-12 col-xs-12">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <label class="col-md-5 col-sm-5 col-xs-10" for="password">{REG-FORM-PASSWORD1-TITLE}:</label>
                                    <input class="col-md-6 col-sm-6 col-xs-10 required password1" name="args[password]" id="password" type="password" placeholder="" <?php echo isset($_POST['args']['password'])? 'value="'.$_POST['args']['password'].'"':''; ?> >
                                </div>
                            </div>
                            <div class="reg-input col-md-12 col-sm-12 col-xs-12">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <label class="col-md-5 col-sm-5 col-xs-10" for="password2">{REG-FORM-PASSWORD2-TITLE}:</label>
                                    <input class="col-md-6 col-sm-6 col-xs-10 required password2" name="args[password]" id="password2" type="password" placeholder="" <?php echo isset($_POST['args']['password'])? 'value="'.$_POST['args']['password'].'"':''; ?> >
                                </div>
                            </div>

                            <div class="reg-input col-md-12 col-sm-12 col-xs-12">
                                <div class="col-md-12 col-sm-12 col-xs-12 hint-password">
                                    {PASSWORD-HINT}
                                </div>
                            </div>

                            <div class="reg-input col-md-12 col-sm-12 col-xs-12">
                                <div class="client-input__data col-md-12 col-sm-12 col-xs-12">
                                    <input name="confirmation" id="confirmation" type="checkbox" class="required">
                                    <label class="col-md-6 col-sm-6 col-xs-6" for="confirmation">{REG-FORM-CONFIRMATION}</label>
                                </div>
                            </div>

                            <button class="btn-press">{REG-FORM-SUBMIT-TITLE}</button>

                        </form>

                        <?php
                    }
                    ?>




                    <?php
                }
                ?>






    </div>
</div>



<script type="text/javascript">
    jQuery(document).ready(function () {
        (function ($) {

            $("[data-mask='phone_number']").mask('+389999999999');

            
            function ValidPassvords(form) {
                if (form.target !== undefined) {
                    var getForm = jQuery(form.target);
                    if(getForm.find('.password1').val() == getForm.find('.password2').val()){
                        getForm.find('.password2').removeClass("nomatch");
                        return true;
                    }
                    else {
                        getForm.find('.password2').addClass("nomatch");
                        return false;
                    }
                }
                return false;
            }
            $(".validate").each(function () {
                $(this).submit(ValidPassvords);
            });


        })(jQuery);
});
</script>