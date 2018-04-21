<?php
include "cabinet_remittance_parameters.php";
?>

<div class="remittance panel panel-default">
    <div class="panel-heading">
        <h1><?= I18N['{CABINET-REMITTANCE-' . $FinancialOperationTypeList[$FinancialOperationTypeActive] . '-HEADER}'] ?></h1>
    </div>

    <div class="panel-body preloader_block preloader_complete">

        <div class="preloader_content">
            <form action="/<?= ($tpl->lang . $IB->CONFIG->get('CABINET_REMITTANCE_URL')) ?>/">
                <div class="form-group row">
                    <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
                    <label class="col-md-3 col-sm-2 col-xs-12 text-right">{CABINET-REMITTANCE}</label>
                    <div class="col-md-4 col-sm-6 col-xs-12">
                        <select class="form-control" name="FinancialOperationType" onchange="this.form.submit()">
                            <?php
                            foreach ($FinancialOperationTypeList as $finance_type => $item) { ?>
                                <option value="<?= $finance_type ?>" <?= (($FinancialOperationTypeActive == $finance_type) ? "selected='selected'" : "") ?>>
                                    {CABINET-REMITTANCE-<?= $item ?>}
                                </option>
                            <?php } ?>
                        </select>

                        <?php
                        if (isset($_GET['data']) && count($_GET['data'])) {
                            foreach ($_GET['data'] as $field => $value) {
                                if (!is_array($value)) echo '<input type="hidden" name="data[' . $field . ']" value="' . $value . '" />';
                            }
                        }
                        ?>
                    </div>
                    <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
                </div>
            </form>
            <hr/>

            <div class="form-templates active">
                <?php
                $SERVICE = isset($_POST['SERVICE']) ? $_POST['SERVICE'] : false;
                $amountInCents = (int)preg_replace('/\D/', '', $data['amountInCents']);

                //Квитанции или повтор операции
                if (isset($data['operationId']) && (($SERVICE === "receipt") or ($SERVICE === "operation"))) {
                    $operation = $IB->request("findFinancialOperationById", [
                        $IB->TOKEN,
                        $data['operationId']
                    ]);

                    if (($SERVICE == "operation") && !$operation) $SERVICE = "form";

                    if ($SERVICE == "receipt") {
                        if (!$operation) {
                            echo "exception";
                            exit;
                        }
                        ob_clean();
                        ob_start();
                    }
                }

                include "cabinet_remittance_" . $FinancialOperationTypeList[$FinancialOperationTypeActive] . ".php";
                if(!empty($financialOperationResult->attributes->attrs)) $EXCEPTION = $IB->error_process($financialOperationResult->attributes->attrs);

                if (!empty($EXCEPTION)) {
                    ?>
                    <div class="cabinet_remittance_<?= (!empty($EXCEPTION_SUCCESS)) ? "success" : "error" ?>"><?= $EXCEPTION ?></div>
                    <?php
                }

                $SERVICE = $SERVICE ? strtr($SERVICE, array("enroll" => "commit")) : "form";
                include "cabinet_remittance_" . $FinancialOperation . "_" . $SERVICE . ".php";

                //Квитанции
                if (isset($data['operationId']) && ($SERVICE === "receipt")) {
                    $html = ob_get_contents();
                    ob_end_clean();

                    $htmlPage = $tpl->ApplyTemplatesIB($html, $tpl, array("IB" => $IB, "I18N" => I18N));
                    $htmlPage = strtr($htmlPage, I18N);

                    echo $htmlPage;
                    exit();
                }
                ?>
            </div>
        </div>

        <img class="preloader" src="<?= $tpl->path ?>/images/preloader.gif"/>
    </div>
</div>

<script>
    jQuery(document).ready(function () {
        window.onbeforeunload = function (evt) {
            jQuery(".remittance .preloader_block").removeClass("preloader_complete");
        };
    });
</script>