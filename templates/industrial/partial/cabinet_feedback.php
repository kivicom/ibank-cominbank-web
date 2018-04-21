<?php
if (isset($_POST['data'])) {
    $data = $_POST['data'];
    $subject = isset($data['subject']) ? htmlentities($data['subject'], ENT_COMPAT) : "";
    $message = isset($data['message']) ? htmlentities($data['message'], ENT_COMPAT) : "";

    if ($subject && $message) {
        $sendMessage = $IB -> request("sendMessage", [
            $IB->TOKEN,
            $subject,
            $message
        ]);


        $EXCEPTION = $IB -> EXCEPTION_process();

        $SUCCESS_MESSAGE = '{CABINET-FEEDBACK-SEND-MESSAGE-STATUS-'.$sendMessage -> operationInfo -> status.'}';
    }
}

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



$inbox = isset($_GET['inbox']) ? (($_GET['inbox'] == "1") ? true : false) : true;
?>
<div class="card-limits-main form-templates">
    <form id="filter-feedback" class="col-md-12 col-sm-12 col-xs-12 form-with-head validate" method="get" onchange="jQuery(this).submit()">
        <div class="title">
            <div class="title-content form-inline nofloat col-md-12 col-lg-12 col-sm-12 col-xs-12">
                <div class="col-md-5 col-lg-5 col-sm-6 col-xs-12 align-left">
                    <span>{CABINET-FEEDBACK-LIST-MESSAGES}</span>
                    <select class="form-control" name="inbox">
                        <option value="1" <?=($inbox ? "selected='selected'" :"")?>>{CABINET-FEEDBACK-INBOX}</option>
                        <option value="0" <?=(!$inbox ? "selected='selected'" :"")?>>{CABINET-FEEDBACK-OUTBOX}</option>
                    </select>
                </div>
                <div class="col-md-4 col-lg-4 col-sm-6 col-xs-12"></div>
                <div class="col-md-3 col-lg-3 col-sm-6 col-xs-12 align-right">
                    <button type="button" class="form-control btn-default filter-feedback__sendMessage" data-toggle="modal" data-target="#sendMessage"><b class="glyphicon glyphicon-pencil"></b> {CABINET-FEEDBACK-WRITE-MESSAGE}</button>
                </div>
            </div>
        </div>

        <?php
        if ($inbox) {

            $OfferFilter = new OfferFilter();
            $OfferFilter -> channelType = 99;
            $OfferFilter -> allowedStatuses = array(1,2,3,4,5);
            $OfferFilter -> havingResponses = 2;
            $OfferFilter -> havingCallToActionOperation = 2;


            $fetchAllOffers = $IB -> request("fetchAllOffers", [
                $IB->TOKEN,
                $OfferFilter,
                0,
                100,
                new \Attributes()
            ]);

            if ($fetchAllOffers) {
                ?>
                <div class="feedback_block preloader_block preloader_complete">

                    <div class="preloader_content">
                        <?php
                        if (count($fetchAllOffers)) {
                            foreach ($fetchAllOffers as $index => $item) {
                                if ($index) echo "<hr/>";
                                ?>
                                <div class="feedback__item">
                                    <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 feedback__item_content">
                                        <div><strong><?=$item -> additionalContentParts['subject']->body?></strong></div>
                                        <div><?=$item -> additionalContentParts['content']->body?></div>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            ?>
                            <div>{CABINET-FEEDBACK-EMPTY}</div>
                            <?php
                        }
                        ?>
                    </div>

                    <img class="preloader" src="<?=$tpl->path?>/images/preloader.gif" />
                </div>

                <?php
            }
        } else {
            ?>
            <?php
            $OperationHistoryFilter = new OperationHistoryFilter();
            $fetchFeedbacksHistoryWithLastDays = $IB -> request("fetchFeedbacksHistoryWithLastDays", [
                $IB->TOKEN,
                $OperationHistoryFilter,
                10,
                new \Attributes()
            ]);

            if ($fetchFeedbacksHistoryWithLastDays) {
                ?>
                <div class="feedback_block preloader_block preloader_complete">

                    <div class="preloader_content">
                        <?php
                        if (count($fetchFeedbacksHistoryWithLastDays)) {
                            for($index = count($fetchFeedbacksHistoryWithLastDays) - 1; $index >= 0; $index--) {
                                $item = $fetchFeedbacksHistoryWithLastDays[$index];

                                $created = isset($item -> created) ? $item -> created/1000 : "";
                                $createdD = date("d.m.Y", $created);
                                $createdH = date("H:i", $created);
                                ?>
                                <div class="feedback__item">
                                    <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                        <div><span class="glyphicon glyphicon-envelope"></span></div>
                                        <div><?=$createdD?></div>
                                        <div><?=$createdH?></div>
                                        <div><?=(($item -> response === null) ? "Исходящее" : "Входящее")?></div>
                                    </div>
                                    <div class="col-lg-9 col-md-9 col-sm-9 col-xs-12 feedback__item_content">
                                        <div><strong><?=$item -> subject?></strong></div>
                                        <div><?=$item -> originalRequest?></div>
                                    </div>
                                </div>
                                <?php
                                if ($index) echo "<hr/>";
                            }
                        } else {
                            ?>
                            <div>{CABINET-FEEDBACK-EMPTY}</div>
                        <?php
                        }


                        ?>
                    </div>

                    <img class="preloader" src="<?=$tpl->path?>/images/preloader.gif" />
                </div>

                <?php
            }
            ?>
            <?php
        }
        ?>
    </form>
</div>

<div id="sendMessage" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">{CABINET-FEEDBACK-WRITE-MESSAGE}</h4>
            </div>
            <div class="modal-body">
                <form class="validate" method="post">
                    <div class="form-group">
                        <label>{CABINET-FEEDBACK-THEME}</label>
                        <input class="required form-control"
                               name="data[subject]" type="text" placeholder=""
                               value="">
                    </div>
                    <div class="form-group">
                        <label>{CABINET-FEEDBACK-MESSAGE}</label>
                        <textarea class="input-count required form-control" name="data[message]" type="text" placeholder=""></textarea>
                    </div>
                    <button type="submit" class="btn-press">{CABINET-FEEDBACK-SEND-MESSAGE}</button>
                </form>
            </div>
        </div>
    </div>
</div>
