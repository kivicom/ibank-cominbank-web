<?php
$EXCEPTION = false;
if (isset($_GET['AJAX'])) {
    if (isset($_GET['method'])) {
        $data = $_POST['data'];

        if ($_GET['method'] == "recovery") {
            if (isset($data['phone'])) {
                $ClientIdentificationType = new \ClientIdentificationType();
                $PasswordResetForm = new \PasswordResetForm();
                $PasswordResetForm->clientIdentifier = $data['phone'];
                $PasswordResetForm->birthday = 1000 * (time() - 3600*24*365*30);
                $PasswordResetForm->identifierType = 1;
                $PasswordResetForm->email = $data['email'];
                $PasswordResetForm->login = $data['phone'];
                $PasswordResetForm->phone = $data['phone'];

                $requestPasswordReset = $IB->request("requestPasswordReset", [
                    $PasswordResetForm,
                    new \Attributes()
                ]);

                if ($requestPasswordReset) {
                    $arr = array("request" => "success", "response" => $requestPasswordReset);
                    echo json_encode($arr);
                    exit();
                }
                $EXCEPTION = $IB->EXCEPTION_process();
                if ($EXCEPTION) {
                    $arr = array("request" => "error", "EXCEPTION" => $EXCEPTION);
                    echo json_encode($arr);
                    exit();
                }
            }

            $arr = array("request" => "error");
            echo json_encode($arr);
            exit();
        }

        if ($_GET['method'] == "reset") {
            if (isset($data['otp']) && isset($_POST['token']) && $_POST['token']) {
                $resetPassword = $IB->request("resetPassword", [
                    $_POST['token'],
                    $data['otp'],
                    $data['password'],
                    $data['confirm'],
                    new \Attributes()
                ]);

                if ($resetPassword) {
                    $arr = array("request" => "success", "response" => $resetPassword);
                    echo json_encode($arr);
                    exit();
                }

                $EXCEPTION = $IB->EXCEPTION_process();
                if ($EXCEPTION) {
                    $arr = array("request" => "error", "EXCEPTION" => $EXCEPTION);
                    echo json_encode($arr);
                    exit();
                }

            }

            $arr = array("request" => "error");
            echo json_encode($arr);
            exit();
        }
    }
}



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

if (isset($_POST['args']) && isset($_POST['method'])) {
    $args = $_POST['args'];

    switch ($_POST['method']) {
        case "authenticate":
            $Challenge = new \Challenge();
            $Attributes = new \Attributes();

            $IB->SESS = array();
            $AuthCredentials = new \AuthCredentials();
            $AuthCredentials -> login = "+".preg_replace('/\D/', '', isset($args[0]) ? $args[0] : "");
            $AuthCredentials -> password = isset($args[1]) ? $args[1] : "";
            $AuthCredentials -> type = 1;

            $result = $IB -> request(
                "authenticate",
                array(
                    $AuthCredentials,
                    $Challenge,
                    $Attributes,
                    '',
                    '',
                    '',
                    ''
                ));

            $EXCEPTION = $IB->EXCEPTION_process();

            if ($result) {
                $IB->SESS['TOKEN'] = isset($result -> token) ? $result -> token : '';
                $IB->TOKEN = $IB->SESS['TOKEN'];
                $IB->SESS['USER'] = isset($result -> userInfo) ? (array) $result -> userInfo : false;
                $IB->EXCEPTION = false;
                $tpl -> SESSION();

                if (isset($_POST['redirect'])) {
                    ?>
                    <form id="redirect" method="post" action="<?=$_POST['redirect']?>">
                        <input type="hidden" name="TOKEN" value="<?=$IB->SESS['TOKEN']?>" />
                        <?php
                        if (isset($_POST['redirect_auth'])) {
                            ?>
                            <input type="hidden" name="redirect" value="<?=$tpl -> url?>" />
                            <?php
                        }
                        ?>
                    </form>
                    <script>
                        var form = document.getElementById("redirect");
                        form.submit();
                    </script>
                    <?php
                    exit();
                }
            }
            break;

        case "open_reset_pass":
            ?>
            <script>
                jQuery(document).ready(function () {
                    jQuery('#password_reset_form').addClass('hidden');
                    jQuery('#password_reset_success').addClass('hidden');
                    jQuery("#password_recovery").modal();
                });
            </script>
            <?php
            break;
    }
} else {
    if (isset($_GET['logout'])) {
        $Challenge = new \Challenge();
        $Attributes = new \Attributes();

        $result = $IB -> request(
            "logout",
            array(
                $IB->TOKEN,
                $Attributes
            ));
        $IB -> SESS = array();
        $tpl -> SESSION();

        header('Location: '.$IB -> CONFIG -> get('URL_WEBSITE'));
        exit();
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
    function SuccessExit($IB, $tpl){
        $returnArray = array();
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
                else{
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
        $phoneMask = SuccessExit($IB, $tpl);
    }


}
?>
<section class="login-sec cf">
    <div class="container">
        <div class="login-sec__form">
            <div class="overview overview_login">
                <div class="overview__inner">

                    <?php
                    if (!empty($phoneMask) || !empty($sms_sent)){
                        ?>

                        <div class="services">
                            <div class="services__name form_header">
                                <span>{LOGIN-TO-PHONE} <?=$phoneMask?> {TEXT-SMS-SENT}</span>
                            </div>
                        </div>
                        <div class="sub-services">
                            <form class="form-auth validate" action="" method="post">
                                <input type="hidden" name="method" value="activateSuretyrecords" />
                                <div class="sub-services__acts">
                                    <div class="client-input">
                                        <div class="client-input__data phone">
                                            <label for="sms_code">{SURETY-RECORD-SMS-CODE}:</label>
                                            <input class="required" name="sms_code" id="sms_code" type="text" placeholder="" >
                                        </div>
                                    </div>
                                    <button class="btn-press">{TEXT-SUBMIT}</button>
                                </div>
                            </form>
                        </div>

                        <?php
                    }
                    else{

                        ?>

                        <div class="services">
                            <div class="services__name form_header">
                                <span>{LOGIN-FORM-TITLE}</span>
                            </div>
                        </div>
                        <?php if($EXCEPTION){ ?><div class="cabinet_remittance_error"><?= $EXCEPTION ?></div><?php } ?>
                        <div class="sub-services">
                            <form class="form-auth validate" action="" method="post" onsubmit='jQuery("#phone_hidden").val(jQuery("#phone").val().replace(/[^0-9+]/g, ""))'>
                                <input type="hidden" name="method" value="authenticate" />
                                <div class="sub-services__acts">
                                    <div class="client-input">
                                        <div class="client-input__data phone">
                                            <label for="phone">{LOGIN-FORM-PHONE-TITLE}:</label>
                                            <input id="phone_hidden"  type="hidden" name="args[0]" />
                                            <input id="phone" class="required" data-mask='phone' value="" type="tel" placeholder="+38 (___) ___ __ __">
                                        </div>
                                        <div class="client-input__data password">
                                            <label for="password">{LOGIN-FORM-PHONE-PASS}:</label>
                                            <input name="args[1]" id="password" class="required" value="" type="password" placeholder="">
                                            <div class="restore">
                                                <a href="<?=$tpl->lang;?>/cabinet/reg"><span>{LOGIN-FORM-REGISTER}</span></a>
                                                <a href="#" data-toggle="modal" data-target="#password_recovery" onclick="jQuery('#password_recovery_form').removeClass('hidden');jQuery('#password_reset_form').addClass('hidden');jQuery('#password_reset_success').addClass('hidden');"><span>{LOGIN-FORM-RECOVERY}</span></a>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="btn-press">{LOGIN-FORM-SIGNIN}</button>
                                </div>
                            </form>

                            <div class="load-apps">
                                <span class="load-apps__title">{LOGIN-FORM-APP}</span>
                                <span>
                                    <a href="<?php echo $IB -> CONFIG -> get('ANDROID_APP') ?>" target="_blank">
                                        <i class="icons icons_android"></i>
                                    </a>
                                    <a href="<?php echo $IB -> CONFIG -> get('IOS_APP') ?>" target="_blank">
                                        <i class="icons icons_apple"></i>
                                    </a>
                            </span>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <div id="password_recovery" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{LOGIN-FORM-RECOVERY}</h4>
                </div>
                <div class="modal-body preloader_block preloader_complete">
                    <div class="cabinet_remittance_error hidden"></div>
                    <div class="preloader_content">
                        <form id="password_recovery_form" class="form-auth validate" action="" method="post">
                            <div class="sub-services__acts">
                                <div class="client-input">
                                    <div class="client-input__data">
                                        <label for="phone">{LOGIN-FORM-PHONE-TITLE}:</label>
                                        <input id="phone_hidden_rec" type="hidden" name="data[phone]" />

                                        <input id="phone_rec" class="required" data-mask='phone' value="" type="text" placeholder="+38 (___) ___ __ __" />
                                    </div>

                                    <div class="client-input__data">
                                        <label for="email">{PASSWORD-RECOVERY-EMAIL}:</label>
                                        <input class="required email" name="data[email]" value="" id="email" type="email"  />
                                    </div>
                                </div>
                                <div class="login-buttons">
                                    <button type="button" class="btn-press btn-press_recov" onclick="password_recovery_send()"><span class="recovery-btn">{LOGIN-FORM-RECOVERY}</span></button>
                                    <button type="button" class="btn-press cancel" data-dismiss="modal"><span>{LOGIN-FORM-CANCEL}</span></button>
                                </div>
                            </div>
                        </form>

                        <form id="password_reset_form" class="form-auth hidden" action="" method="post">
                            <input type="hidden" name="token" value="" />
                            <div class="sub-services__acts">
                                <div class="client-input">
                                    <div class="client-input__data">
                                        <label for="password">{PASSWORD-RECOVERY-OTP}:</label>
                                        <input name="data[otp]" class="required" value="" type="text" placeholder="">
                                    </div>

                                    <div class="client-input__data">
                                        <label for="password">{PASSWORD-RECOVERY-PASSWORD}:</label>
                                        <input class="required" name="data[password]" value="" type="password"  />
                                    </div>

                                    <div class="client-input__data">
                                        <label for="confirm">{PASSWORD-RECOVERY-CONFIRM}:</label>
                                        <input class="required" name="data[confirm]" value="" type="password"  />
                                    </div>
                                </div>

                                <button type="button" class="btn-press" onclick="password_reset_send()">{LOGIN-FORM-RECOVERY}</button>
                            </div>
                        </form>

                        <div id="password_reset_success">
                            <div class="password_reset_success__content">{PASSWORD-RECOVERY-SUCCESS}</div>
                            <button type="button" class="btn-press" data-dismiss="modal">Ok</button>
                        </div>

                    </div>
                    <img class="preloader" src="<?=$tpl->path?>/images/preloader.gif" />
                </div>
            </div>
        </div>
    </div>
</section>


<script>
    function password_recovery_send() {
        var modal = jQuery("#password_recovery");
        modal.find(".cabinet_remittance_error").addClass("hidden");
        modal.find(".cabinet_remittance_error").html("");

        jQuery("#phone_hidden_rec").val(jQuery("#phone_rec").val().replace(/[^0-9+]/g, ""));

        if (validation({target: password_recovery_form})){
            modal.find(".preloader_block").removeClass("preloader_complete");

            jQuery.ajax({
                url: '/index.php?AJAX&TEMPLATE=login_form&method=recovery',
                method: "post",
                dataType: "json",
                data: jQuery("#password_recovery_form").serialize(),
                success: function(r){
                    if (r.request !== undefined) {
                        setTimeout(function () {
                            modal.find(".preloader_block").addClass("preloader_complete");
                            modal.find("input[name='otp']").focus();
                        }, 1000);

                        if (r.request == "success") {
                            jQuery("#password_recovery_form").addClass("hidden");
                            jQuery("#password_reset_form").removeClass("hidden");
                            initPasswordsInputs();
                            jQuery("#password_reset_form input[name='token']").val(r.response.token);
                        } else if ((r.request == "error") && (r.EXCEPTION !== undefined)) {
                            modal.find(".cabinet_remittance_error").removeClass("hidden");
                            modal.find(".cabinet_remittance_error").html(r.EXCEPTION)
                        } else oops();
                    } else oops();
                }
            });
        }
        return false;

    }

    function password_reset_send() {
        var modal = jQuery("#password_recovery");
        modal.find(".cabinet_remittance_error").addClass("hidden");
        modal.find(".cabinet_remittance_error").html("");

        if (validation({target: password_reset_form})){
            modal.find(".preloader_block").removeClass("preloader_complete");

            jQuery.ajax({
                url: '/index.php?AJAX&TEMPLATE=login_form&method=reset',
                method: "post",
                dataType: "json",
                data: jQuery("#password_reset_form").serialize(),
                success: function(r){
                    if (r.request !== undefined) {
                        setTimeout(function () {
                            modal.find(".preloader_block").addClass("preloader_complete");
                        }, 1000);

                        if (r.request == "success") {
                            jQuery("#password_recovery_form").addClass("hidden");
                            jQuery("#password_reset_form").addClass("hidden");
                            jQuery("#password_reset_success").removeClass("hidden");
                        } else if ((r.request == "error") && (r.EXCEPTION !== undefined)) {
                            modal.find(".cabinet_remittance_error").removeClass("hidden");
                            modal.find(".cabinet_remittance_error").html(r.EXCEPTION)
                        } else oops();
                    } else oops();
                }
            });
        }
        return false;
    }
</script>