<?php
include "cabinet_remittance_parameters.php";

$SERVICE = isset($_POST['SERVICE']) ? $_POST['SERVICE'] : false;
$SERVICE = $SERVICE ? $SERVICE : "repeat_template";
$getParameters = $data;
$DISABLED = [];

$templateId = isset($_POST['templateId']) ? $_POST['templateId'] : "";
$operationId = isset($_POST['operationId']) ? $_POST['operationId'] : "";

if ($templateId) {
    $template = $IB->request("findTemplateById", [
        $IB->TOKEN,
        $templateId
    ]);

    if ($template) {
        $amountInCents = isset($template->definedAmounts[0]->amount) ? $template->definedAmounts[0]->amount : 0;

        $fields = array_merge(
            ['title' => $template -> title],
            ['description' => $template -> description],
            (array) $template->defaultSourceContract,
            ['currency' => $template->currency],
            ['amountInCents' => $amountInCents]
        );

        foreach ($template->operationSubject as $fieldO => $values) {
            if (is_object($values)) {
                $values = (array) $values;
                foreach ($values as $field => $value) {
                    if (!isset($fields[$field]) && !is_object($value)) $fields[$field] = $value;

                    //Для биллеров
                    if (($fieldO == "subjectBiller") && isset($values['parameters']) && isset($values['parameters'] -> attrs)) {
                        foreach ($values['parameters'] -> attrs as $filedB => $valueB) {
                            if (!isset($fields[$filedB]) && !is_object($valueB)) $fields[$filedB] = $valueB;
                        }
                        $data['billerId'] = $getParameters['billerId'] = $values['billerId'];
                        $data['providerId'] = $getParameters['providerId'] = $values['providerId'];
                        include "cabinet_remittance_BILLER_PAYMENT_phone_number.php";
                    }

                    //Для операций CONTRACT-TO-CONTRACT
                    if ($fieldO == "subjectContract") {
                        $fields['id_to'] = $values['id'];
                        $fields['type_to'] = $values['type'];
                    }

                    //Для SEP
                    if ($fieldO == "subjectSEP") {
                        $data['destinationDescription'] = $getParameters['destinationDescription'] = $template->operationSubject->subjectDescription;
                    }
                }
            }
        }
        foreach ($fields as $field => $fieldValue) {
            $getParameters[$field] = $fieldValue;
        }
    }
}elseif($operationId){
    $financialOperation = $IB->request("findFinancialOperationById", [
        $IB->TOKEN,
        $operationId
    ]);
    if ($financialOperation) {

        $fields = array_merge(
            ['title' => $financialOperation -> name],
            ['description' => $financialOperation -> description],
            ['currency' => $financialOperation->currency],
            ['amountInCents' => $financialOperation->amountInCents]
        );

        foreach ($financialOperation->subject as $fieldO => $values) {
            if (is_object($values)) {
                $values = (array) $values;
                foreach ($values as $field => $value) {
                    if (!isset($fields[$field]) && !is_object($value)) $fields[$field] = $value;

                    //Для биллеров
                    if (($fieldO == "subjectBiller") && isset($values['parameters']) && isset($values['parameters'] -> attrs)) {
                        foreach ($values['parameters'] -> attrs as $filedB => $valueB) {
                            if (!isset($fields[$filedB]) && !is_object($valueB)) $fields[$filedB] = $valueB;
                        }
                        $data['billerId'] = $getParameters['billerId'] = $values['billerId'];
                        $data['providerId'] = $getParameters['providerId'] = $values['providerId'];
                        include "cabinet_remittance_BILLER_PAYMENT_phone_number.php";
                    }

                    //Для операций CONTRACT-TO-CONTRACT
                    if ($fieldO == "subjectContract") {
                        $fields['id_to'] = $values['id'];
                        $fields['type_to'] = $values['type'];
                    }

                    //Для SEP
                    if ($fieldO == "subjectSEP") {
                        $data['destinationDescription'] = $getParameters['destinationDescription'] = $financialOperation->subject->subjectDescription;
                    }
                }
            }
        }
        foreach ($fields as $field => $fieldValue) {
            $getParameters[$field] = $fieldValue;
        }
        if(is_object($financialOperation->srcContractRef)){
            $getParameters['id'] = $financialOperation->srcContractRef->id;
            $getParameters['type'] = $financialOperation->srcContractRef->type;
        }
    }

}else{
    include "cabinet_remittance_BILLER_PAYMENT_phone_number.php";
}
$formId = "form_".uniqid();

if ($SERVICE == "repeat_template") {
    ?>
    <script src="/media/jui/js/jquery.min.js"></script>
    <script>
        jQuery(document).ready(function () {
            jQuery("#<?=$formId?>").submit();
        });
    </script>
    Redirection...
    <?php
}
?>

<form id="<?=$formId?>" method="post" action="/<?= ($tpl->lang . $IB->CONFIG->get('CABINET_REMITTANCE_URL')) ?>?FinancialOperationType=<?=$FinancialOperationTypeActive?>" class="form-horizontal template-ajax-form" <?=($SERVICE == "repeat_template") ? "style='display:none;'" : $SERVICE ?>>
    <input type="hidden" name="SERVICE" value="<?=($SERVICE == "repeat_template") ? "" : $SERVICE ?>" />

    <?php
    if (isset($_POST['templateId'])) echo '<input type="hidden" name="data[templateId]" value="'.$_POST['templateId'].'" />';
    if (isset($_POST['operationId'])) echo '<input type="hidden" name="operationId" value="'.$_POST['operationId'].'" />';

    $IB -> includes("blocks/remittance_form_input_default", [
        'label' => '{CABINET-TEMPLATES-NEW-NAME}',
        'data' => $getParameters,
        'required' => ($SERVICE === "delete_template") ? false : true,
        'parameter' => 'title'
    ]);

    $IB -> includes("blocks/remittance_form_textarea_default", [
        'label' => '{CABINET-TEMPLATES-NEW-DESCRIPTION}',
        'data' => $getParameters,
        'parameter' => 'description',
        'required' => false,
        'maxlength' => 255
    ]);

    $disabled = false;
    $DISABLED['amountInCents'] = false;
    $DISABLED['contract_list'] = false;
    include "cabinet_remittance_".$FinancialOperation."_fields.php";

    $IconSelectId = "IconSelect".uniqid();
    $color = "#053F32";
    if (isset($template) && isset($template -> color)) $color = $template -> color;

    $icon = "";
    if (isset($template) && isset($template -> iconRef)) $icon = $template -> iconRef;

    ?>

    <div class="form-group row">
        <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
        <label class="col-md-3 col-sm-3 col-xs-12 text-left">{CABINET-TEMPLATES-EDIT-COLOR}</label>
        <div class="col-md-7 col-sm-7 col-xs-12 text-left">
            <input type="hidden" name="data[color]" class="form-control paletteColorPicker" data-palette='["#0F8DFC","rgba(135,1,101)","#F00285","hsla(190,41%,95%,1)","#94B77E","#4C060A","#053F32","#ED8074","#788364"]' value="<?=$color?>">
        </div>
        <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
    </div>

    <div class="form-group row">
        <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
        <label class="col-md-3 col-sm-3 col-xs-12 text-left">{CABINET-TEMPLATES-EDIT-ICON}</label>
        <div class="col-md-7 col-sm-7 col-xs-12">
            <div id="<?=$IconSelectId?>" class="IconSelect" data-value="<?=$icon?>"
                 data-selected_icon_width="42"
                 data-selected_icon_height="42"
                 data-selected_box_padding="1"
                 data-icons_width="34"
                 data-icons_height="34"
                 data-box_icon_space="1"
                 data-vectoral_icon_number="2"
                 data-horizontal_icon_number="6"
                <?php
                if (file_exists($tpl -> pathFull."/images/template-icons")){
                    $handle = opendir($tpl -> pathFull."/images/template-icons");
                    $indexFile = 0;
                    while (false !== ($getFile = readdir($handle))) {
                        if (strpos($getFile, ".svg") !== false) {
                            echo " data-img_".++$indexFile."='".$tpl->path."/images/template-icons/".$getFile."'  data-val_".$indexFile."='".(strtr($getFile, array(".svg"=>"")))."'";
                        }
                    }
                    closedir($handle);
                }
                ?>
            ></div>
            <input id="<?=$IconSelectId?>_val" type="hidden" name="data[iconRef]" value="" />
        </div>
        <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
    </div>

    <div class="form-group row">
        <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
        <label class="col-md-3 col-sm-3 col-xs-12 text-left">
            <button type="button" class="btn btn-danger" onclick="jQuery(this).closest('form').find('.remittance_template_delete').slideDown();">{CABINET-TEMPLATES-EDIT-TEMPLATE-DELETE}</button>
        </label>
        <div class="col-md-7 col-sm-7 col-xs-12 text-right">
            <button type="submit" class="btn btn-def" onclick="jQuery('#<?=$IconSelectId?>_val').val(jQuery('#<?=$IconSelectId?>').attr('data-value'));">{CABINET-TEMPLATES-EDIT-TEMPLATE-SAVE}</button>
        </div>
        <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
    </div>

    <div class="form-group row remittance_template_delete" style="display: none;">
        <div class="col-md-12 col-sm-12 col-xs-12">
            <a href="#" onclick="jQuery(this).closest('form').find('input[name=\'SERVICE\']').val('delete_template'); jQuery(this).closest('form').submit();">{CABINET-TEMPLATES-EDIT-TEMPLATE-DELETE-CONFIRM}</a>
        </div>
    </div>
</form>

