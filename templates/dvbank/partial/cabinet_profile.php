<?php
$result = $IB -> request("getAuthSession", [
    $IB->TOKEN
]);

if(empty($days)){
    $days = 2;
}
if(!isset($limit)){
    $limit = 50;
}
$history = $IB->request('fetchProfileHistoryWithLastDays', [
    $IB->TOKEN,
    new OperationHistoryFilter(),
    $days,
    new \Attributes()
]);

if (!empty($_POST['data'])){

    $ChangePasswordForm = new \ChangePasswordForm();
    foreach ($_POST['data'] as $key => $password){
        $ChangePasswordForm->{$key} = $password;
    }
    $changePassword = $IB -> request("changePassword", [
        $IB->TOKEN,
        $ChangePasswordForm,
        new \Attributes()
    ]);


    if ($changePassword){
        $SUCCESS_MESSAGE = '{CABINET-PROFILE-PASSWORD-SUCCESS}';
    }

    $EXCEPTION = $IB->EXCEPTION_process();
}

$fullName = isset($result->userInfo->fullName) ? $result->userInfo->fullName : '';
$login = isset($result->userInfo->name) ? $result->userInfo->name : '';
$birthday = !empty($result->userInfo->attributes->attrs['birthday'])? $result->userInfo->attributes->attrs['birthday'] : '';
?>

<div class="client-profile">
    <div class="profile-block">
        <div class="profile-block__title">
            <div class="profile-block__title-icon">
            </div>
            {CABINET-PROFILE-PERSONAL-DATA}
        </div>
        <div class="profile-container">
            <div class="row profile-row">
                <div class="col-md-5 col-sm-4 col-xs-3 col-12 profile-container__col-color">{CABINET-PROFILE-FULLNAME}</div>
                <div class="col-md-6 col-sm-6 col-xs-9 col-12"><?=$fullName?></div>
            </div>
            <div class="row profile-row">
                <div class="col-md-5 col-sm-4 col-xs-3 col-12 profile-container__col-color">{CABINET-PROFILE-LOGIN}</div>
                <div class="col-md-6 col-sm-6 col-xs-9 col-12"><?=$login?></div>
            </div>
            <div class="row profile-row">
                <div class="col-md-5 col-sm-4 col-xs-3 col-12 profile-container__col-color">{CABINET-PROFILE-EMAIL}</div>
                <div class="col-md-6 col-sm-6 col-xs-9 col-12"><?=$result->userInfo->email?></div>
            </div>
            <div class="row profile-row">
                <div class="col-md-5 col-sm-4 col-xs-3 col-12 profile-container__col-color">{CABINET-PROFILE-PHONE}</div>
                <div class="col-md-6 col-sm-6 col-xs-9 col-12"><?=$result->userInfo->phone?></div>
            </div>
            <?php if ($birthday) { ?>
            <div class="row">
                <div class="col-md-5 col-sm-4 col-xs-3 col-12 profile-container__col-color">{CABINET-PROFILE-BIRTHDAY}</div>
                <div class="col-md-6 col-sm-6 col-xs-9 col-12"><?=$result->userInfo->fullName?></div>
            </div>
            <?php } ?>
        </div>
    </div>


    <div class="profile-block profile-block-collapsed">
        <div class="profile-block__title">
            <div class="profile-block__title-icon">
            </div>
            {CABINET-PROFILE-CHANGE-PASSWORD}
        </div>
        <?php if (!empty($SUCCESS_MESSAGE)){ ?>
            <div class="success_action"><?=$SUCCESS_MESSAGE?></div>
        <?php }
        if (!empty($EXCEPTION)) { ?>
            <div class="cabinet_remittance_error">
                <?=$EXCEPTION?>
            </div>
        <?php } ?>
        <div class="profile-container">
            <form class="client-profile__form form-templates validate" method="post">
                <div class="client-profile__content row">
                    <div class="client-input">
                        <label class="col-md-5 col-sm-6 col-xs-6 col-12 profile-container__col-color">{CABINET-PROFILE-CURRENT-PASSWORD}</label>
                        <input class="required col-md-6 col-sm-5 col-xs-6 col-12 password1" data-password="oldpassword" id="password" name="data[oldPassword]" type="password" placeholder="" value="">
                        <div class="col-md-1">
                        </div>
                    </div>
                </div>
                <div class="client-profile__content row">
                    <div class="client-input">
                        <label class="col-md-5 col-sm-6 col-xs-6 col-12 profile-container__col-color">{CABINET-PROFILE-NEW-PASSWORD}</label>
                        <input class="required col-md-6 col-sm-5 col-xs-6 col-12 password2" data-password="password" id="password2" name="data[newPassword]" type="password" placeholder="" value="">
                        <div class="col-md-1">

                        </div>
                    </div>
                </div>
                <div class="client-profile__content row">
                    <div class="client-input">
                        <label class="col-md-5 col-sm-6 col-xs-6 col-12 profile-container__col-color">{CABINET-PROFILE-CONFIRM-PASSWORD}</label>
                        <input class="required col-md-6 col-sm-5 col-xs-6 col-12 password3" data-error-msg="{EXCEPTION-PASSWORDS-DONT-MATCH}" data-password="repeat" id="password3" name="data[confirmPassword]" type="password" placeholder="" value="">
                        <div class="col-md-1">
                        </div>
                    </div>
                </div>
                <div class="text-center">
                    <button class="btn-press">{CABINET-PROFILE-SAVE-PASSWORD}</button>
                </div>
            </form>
        </div>
    </div>

    <div class="profile-block profile-block-collapsed">
        <div class="profile-block__title">
            <div class="profile-block__title-icon">
            </div>
            {CABINET-PROFILE-HISTORY}
        </div>
        <div class="profile-container profile-history">
            <div class="row profile-history-header profile-history-row">
                <div class="col-md-4 col-sm-4 col-xs-4 col-12 profile-history-header">{CABINET-PROFILE-HISTORY-DATE}</div>
                <div class="col-md-4 col-sm-4 col-xs-4 col-12">{CABINET-PROFILE-HISTORY-STATUS}</div>
                <div class="col-md-4 col-sm-4 col-xs-4 col-12">{CABINET-PROFILE-HISTORY-IP}</div>
            </div>
            <?php
            if($history){
            $i = 0;
            foreach ($history as $item){
                if($item->credentialsType == 1 && $item->credentialsType == 1) {
                    $UTC = $item->operationDate/1000;
                    $time = date('H:i', $UTC);
                    $date = date('d.m.Y', $UTC);
                    if($item->operationStatus){
                        $statusText = '{CABINET-PROFILE-HISTORY-FAIL}';
                        $statusClass = 'status-fail';
                    }else{
                        $statusText = '{CABINET-PROFILE-HISTORY-SUCCESS}';
                        $statusClass = 'status-success';
                    }


                ?>
                <div class="row profile-history-row">
                    <div class="col-md-4 col-sm-4 col-xs-4 col-12"><?=$time?><span class="profile-date"><?=$date?></span></div>
                    <div class="col-md-4 col-sm-4 col-xs-4 col-12 <?=$statusClass?>">
                        <?=$statusText?>
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-4 col-12"><?=$item->ipAddress?></div>
                </div>
                <?php
                if(++$i >= $limit) break;
                } } } ?>
        </div>
    </div>
</div>