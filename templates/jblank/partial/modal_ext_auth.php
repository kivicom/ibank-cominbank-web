<?php
if (isset($_GET['AJAX'])) {
    if (isset($_GET['method'])) {
        switch ($_GET['method']){
            case "requestAuthentication":
                $Attributes = new \Attributes();
                $AuthCredentials = new \AuthCredentials();
                $AuthCredentials -> type = 4;
                $AuthCredentials -> token = $IB -> TOKEN;

                $result = $IB -> request(
                    "requestAuthentication",
                    array(
                        $AuthCredentials,
                        $Attributes
                    ));

                if ($IB -> EXCEPTION or !isset($result -> challengeId)){
                    $arr = array("request"=>"exception");
                    echo json_encode($arr);
                    exit;
                }
                else {
                    $IB->SESS['challengeId'] = $result -> challengeId;
                    $tpl -> SESSION();

                    $arr = array("request"=>"success");
                    echo json_encode($arr);
                    exit;
                }
                break;

            case "authenticate":
                if (isset($_POST['otp']) && (trim($_POST['otp']) == "")) {
                    $arr = array("request"=>"exception", "exception" => "wrong_otp_credentials");
                    echo json_encode($arr);
                    exit;
                }

                $Challenge = new \Challenge();
                $Attributes = new \Attributes();

                $AuthCredentials = new \AuthCredentials();
                $AuthCredentials -> type = 2;
                $AuthCredentials -> otp = isset($_POST['otp']) ? $_POST['otp'] : "";
                $AuthCredentials -> token = $IB -> TOKEN;

                $Challenge -> challengeId = $IB->SESS['challengeId'];
                $Challenge -> type = 1;

                $result = $IB -> request(
                    "authenticate",
                    array(
                        $AuthCredentials,
                        $Challenge,
                        $Attributes,
                        "",
                        "",
                        "",
                        ""
                    ));

                if ($IB -> EXCEPTION or !isset($result -> token)){
                    $arr = array("request"=>"exception", "exception" => (isset($IB -> EXCEPTION -> errorMessageKey) ? $IB -> EXCEPTION -> errorMessageKey : ""));
                    echo json_encode($arr);
                    exit;
                } else {
                    $IB->SESS['TOKEN'] = isset($result -> token) ? $result -> token : '';
                    $IB->TOKEN = $IB->SESS['TOKEN'];

                    $arr = array("request"=>"success");
                    echo json_encode($arr);
                    exit;
                }

                break;
        }
    }



} else {
    ?>
    <div id="system_ext_auth_required_modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{MODAL-EXT-AUTH-TITLE}</h4>
                </div>
                <div class="modal-body preloader_block">

                    <div class="preloader_content preloader_content--otp">
                        <p class="sms-text">{MODAL-EXT-AUTH-CONTENT}</p>
                        <div class="block--otp">
                            <div class="client-input">
                                <div class="client-input__data">
                                    <input name="otp" class="form-control required" type="text" placeholder="" value='' />
                                </div>
                            </div>
                            <button type="button" class="btn-press auth_button_submit">Ok</button>
                        </div>
                    </div>

                    <img class="preloader" src="<?=$tpl->path?>/images/preloader.gif" />

                </div>
            </div>

        </div>
    </div>

    <div id="system_ext_auth_required_modal_overflow" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{MODAL-EXT-AUTH-TITLE}</h4>
                </div>
                <div class="modal-body">{MODAL-EXT-AUTH-OVERFLOW}</div>
            </div>

        </div>
    </div>
    <?php
}
?>


