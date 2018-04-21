<?php
/* reg_form.php */
$EXCEPTION = false;
if (isset($_POST['sms_code']) && isset($_POST['method'])) {
    $smsCode = (string)$_POST['sms_code'];
    $result = $IB -> request(
        "activateSuretyRecords",
        array(
            $IB->SESS['TOKEN'],
            $smsCode,
            new \Attributes(),
        ));

    $EXCEPTION = $IB->EXCEPTION_process();
    if ($EXCEPTION){
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

        $antiXss = new AntiXSS();
        $phone = "+".preg_replace('/\D/', '', isset($args['phone']) ? $args['phone'] : "");

        $firstName = isset($args['firstName']) ? $antiXss->xss_clean($args['firstName']) : "";
        $lastName = isset($args['lastName']) ? $antiXss->xss_clean($args['lastName']) : "";
        $middleName = isset($args['middleName']) ? $antiXss->xss_clean($args['middleName']) : "";
        $UserRegistrationForm -> identifierType = 1; // тип регистрации - по номеру телефона регистрациия
        $UserRegistrationForm -> login = $phone; // телефон является логином
        $UserRegistrationForm -> password = isset($args['password']) ? $args['password'] : "";
        $UserRegistrationForm -> firstName = $firstName;
        $UserRegistrationForm -> lastName = $lastName;
        $UserRegistrationForm -> middleName = $middleName;
        $UserRegistrationForm -> email = isset($args['email']) ? $args['email'] : "";
        $UserRegistrationForm -> phone = $phone;

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

        $EXCEPTION = $IB->EXCEPTION_process();

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

    function SuccessExit($IB){
        $findSuretyRecordsForActivation = SuretyRecord($IB);
        if ($findSuretyRecordsForActivation){
            foreach ($findSuretyRecordsForActivation as $suretyRecord){
                if ($suretyRecord->activationChannelType == 1){
                    // sending code via sms
                    $requestActivationOfSuretyRecords = $IB -> request(
                        "requestActivationOfSuretyRecords",
                        array(
                            $IB->TOKEN,
                            new \Attributes(),
                        ));

                    $EXCEPTION = $IB->EXCEPTION_process();
                    if(!$EXCEPTION){
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

    if (hasRole('guest',$result)){
        if (hasRole('bound_to_contragent', $result)){
            //вызов findSuretyRecordsForActivation
            $phoneMask = SuccessExit($IB);
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
                    $phoneMask = SuccessExit($IB);
                }
                else {
                    exitSuretyRecords('{USER-NOT-BOUND-TO-CONTRAGENT}');
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
        $phoneMask = SuccessExit($IB);
    }
}
?>
<div class="registration cabinet-registration">
    <h1>{REG-FORM-TITLE}</h1>

    <div class="registration col-md-6 col-sm-10 col-xs-12">
        <?php if ($EXCEPTION) { ?><div class="cabinet_remittance_error"><?= $EXCEPTION ?></div>
        <?php
        }
        if (!empty($phoneMask) || !empty($sms_sent)){
        ?>

        <div class="services">
            <div class="services__name form_header">
                <span>{LOGIN-TO-PHONE} <?=$phoneMask?> {TEXT-SMS-SENT}</span>
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
        } else{
            if (empty($result)){
            ?>
        <form class="form-reg validate" action="" method="post">

            <input type="hidden" name="method" value="registration" />

            <div class="form-reg__grey-box">
                <div class="reg-input col-md-6 col-sm-10 col-xs-12">
                    <div class="icons">
                        <img src="<?=$tpl->pathTemplate?>/img/content/form-icon1.png">
                    </div>
                    <input class="required" name="args[firstName]" id="firstName" type="text" placeholder="{REG-FORM-FIRSTNAME-TITLE}:" <?php echo (!empty($firstName)) ? 'value="'.$firstName.'"':''; ?> >
                </div>
                <div class="reg-input col-md-6 col-sm-10 col-xs-12">
                    <div class="icons">
                        <img src="<?=$tpl->pathTemplate?>/img/content/form-icon1.png">
                    </div>
                    <input class="required" name="args[lastName]" id="lastName" type="text" placeholder="{REG-FORM-LASTNAME-TITLE}:" <?php echo (!empty($lastName)) ? 'value="'.$lastName.'"':''; ?> >
                </div>
                <div class="reg-input col-md-6 col-sm-10 col-xs-12">
                    <div class="icons">
                        <img src="<?=$tpl->pathTemplate?>/img/content/form-icon2.png">
                    </div>
                    <input class="required" name="args[phone]" id="phone" type="tel" placeholder="{REG-FORM-PHONE-TITLE}:" data-mask="phone" placeholder="+38 (___) ___ __ __" autocomplete="off" maxlength="19" <?php echo isset($_POST['args']['phone'])? 'value="'.$_POST['args']['phone'].'"':''; ?> >
                </div>
                <div class="reg-input col-md-6 col-sm-10 col-xs-12">
                    <div class="icons">
                        <img src="<?=$tpl->pathTemplate?>/img/content/form-icon3.png">
                    </div>
                    <input class="required email" name="args[email]" id="email" type="email" placeholder="{REG-FORM-EMAIL-TITLE}:" <?php echo isset($_POST['args']['email'])? 'value="'.$_POST['args']['email'].'"':''; ?> >

                </div>
            </div>

            <div class="form-reg__grey-box">
                <div class="reg-input col-md-6 col-sm-10 col-xs-12">
                    <div class="icons">
                        <img src="<?=$tpl->pathTemplate?>/img/content/form-icon1.png">
                    </div>
                    <input class="required password1" name="args[password]" id="password" type="password" placeholder="{REG-FORM-PASSWORD1-TITLE}:" <?php echo isset($_POST['args']['password'])? 'value="'.$_POST['args']['password'].'"':''; ?> >

                </div>
                <div class="reg-input col-md-6 col-sm-10 col-xs-12">
                    <div class="icons">
                        <img src="<?=$tpl->pathTemplate?>/img/content/form-icon1.png">
                    </div>
                    <input class="required password2" name="args[password]" id="password2" type="password" placeholder="{REG-FORM-PASSWORD2-TITLE}:" <?php echo isset($_POST['args']['password'])? 'value="'.$_POST['args']['password'].'"':''; ?> >

                </div>
            </div>

            <div class="agreement-hint">
                <span>{LOGIN-FORM-REGISTER-AGREE} </span>
                <a href="<?=$IB -> CONFIG -> get('CONDITIONS_URL')?>">{LOGIN-FORM-REGISTER-AGREEMENT}</a>
            </div>

            <button class="btn-press">{REG-FORM-SUBMIT-TITLE}</button>

        </form>
            <?php
            }
        }
        ?>
    </div>
</div>



<script type="text/javascript">
    jQuery(document).ready(function () {
        (function ($) {

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