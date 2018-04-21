<?php
$result = $IB -> request("getAuthSession", [
    $IB->TOKEN
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
}

?>
<div class="client-profile">
    <?php
    if (!empty($SUCCESS_MESSAGE)){
                ?>
                <div class="success_action"><?=$SUCCESS_MESSAGE?></div>
                <?php
            }
    ?>
    <form class="client-profile__form card-limits form-templates form-with-head card-limits_uneditable validate" method="post">
        <div class="title">
            <div class="title-content">
                <span>{CABINET-PROFILE-PERSONAL-DATA}</span>
            </div>
        </div>
        <div class="client-profile__content">
            <?php
            if(!empty($result->userInfo->fullName)) {
                ?>
                <div class="client-input">
                    <label class="col-md-4 col-sm-4 col-xs-12">{CABINET-PROFILE-FULLNAME}</label>
                    <input class="required col-md-6 col-sm-6 col-xs-12" name="data[subject]" type="text" placeholder=""
                           value="<?= $result->userInfo->fullName ?>">
                </div>
                <?php
            }
            if(!empty($result->userInfo->attributes->attrs->birthday)){
                $birthday = date("d.m.Y", $result->userInfo->attributes->attrs->birthday);
                ?>
                <div class="client-input">
                    <label class="col-md-4 col-sm-4 col-xs-12">{CABINET-PROFILE-BIRTHDAY}</label>
                    <input class="required col-md-6 col-sm-6 col-xs-12" name="data[subject]" type="text" placeholder="" value="<?=$birthday?>">
                </div>
                <?php
            }
            ?>

        </div>




    </form>

    <form class="client-profile__form card-limits form-templates form-with-head validate" method="post">
        <div class="title">
            <div class="title-content">
                <span>{CABINET-PROFILE-CONTACT-DATA}</span>
<!--
                <div class="change-limits-button">
                    <i class="icons icons_pencil-limits limits-change"></i>
                    <i class="icons icons_check limits-change limits-change_uneditable"></i>
                </div>  -->
            </div>
        </div>

        <div class="client-profile__content">
            <div class="client-input">
                <label class="col-md-3 col-sm-4 col-xs-12">{CABINET-PROFILE-EMAIL}</label>
                <input class="required col-md-6 col-sm-6 col-xs-12" name="data[subject]" type="text" placeholder="" value="<?=$result->userInfo->email?>" disabled>
                <div class="col-md-3">
<!--                    <button class="edit-buttons">-->
<!--                        <a href="#">Підтвердити e-mail</a>-->
<!--                    </button>-->
                </div>
            </div>
            <div class="client-input">
                <label class="col-md-3 col-sm-4 col-xs-12">{CABINET-PROFILE-PHONE}</label>
                <input class="required col-md-6 col-sm-6 col-xs-12" name="data[subject]" type="text" placeholder="" value="<?=$result->userInfo->phone?>" disabled>

            </div>
            <?php
            if(!empty($result->userInfo->name)){
            ?>
            <div class="client-input">
                <label class="col-md-3 col-sm-4 col-xs-12">{CABINET-PROFILE-LOGIN}</label>
                <input class="required col-md-6 col-sm-6 col-xs-12" name="data[subject]" type="text" placeholder="" value="<?=$result->userInfo->name?>" disabled>
                <div class="col-md-3">
<!--                    <button class="edit-buttons">-->
<!--                        <a href="#">Змінити пароль</a>-->
<!--                    </button>-->
                </div>
            </div>
            <?php
            }
            ?>


            <!--
            <div class="client-input add-new-info">
                <div class="col-md-3">
                    <select class="reload form-control add-new-info__select">
                        <option>телефон</option>
                        <option>e-mail</option>
                    </select>
                </div>

                <input class="col-md-3" type="text" placeholder="" value="">
                <div class="col-md-3">
                    <button class="edit-buttons edit-buttons_red">
                        <a href="#">Видалити</a>
                    </button>
                </div>
            </div>
            <button class="edit-buttons edit-buttons_with-icon">
                <i class="icons icons_plus-alt-grey"></i>
                <a href="#">Додати ще контакт</a>
            </button>

            -->

            <!--<div class="col-md-5">-->
            <!---->
            <!--</div>-->
        </div>





    </form>

    <form class="client-profile__form card-limits form-templates form-with-head card-limits_uneditable validate" method="post">
        <div class="title">
            <div class="title-content">
                <span>{CABINET-PROFILE-CHANGE-PASSWORD}</span>
                <div class="change-limits-button">
                    <i class="icons icons_pencil-limits limits-change"></i>
                    <button class="icons icons_check limits-change limits-change_uneditable"></button>
                </div>
            </div>
        </div>

        <div class="client-profile__content">
            <div class="client-input">
                <label class="col-md-5 col-sm-4 col-xs-12">{CABINET-PROFILE-CURRENT-PASSWORD}</label>
                <input class="required col-md-6 col-sm-6 col-xs-12" name="data[oldPassword]" type="password" placeholder="" value="">
                <div class="col-md-1">
                </div>
            </div>
        </div>
        <div class="client-profile__content">
            <div class="client-input">
                <label class="col-md-5 col-sm-4 col-xs-12">{CABINET-PROFILE-NEW-PASSWORD}</label>
                <input class="required col-md-6 col-sm-6 col-xs-12" name="data[newPassword]" type="password" placeholder="" value="">
                <div class="col-md-1">

                </div>
            </div>
        </div>
        <div class="client-profile__content">
            <div class="client-input">
                <label class="col-md-5 col-sm-4 col-xs-12">{CABINET-PROFILE-CONFIRM-PASSWORD}</label>
                <input class="required col-md-6 col-sm-6 col-xs-12" name="data[confirmPassword]" type="password" placeholder="" value="">
                <div class="col-md-1">

                </div>
            </div>
        </div>
    </form>

</div>