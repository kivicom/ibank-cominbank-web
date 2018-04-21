<?php
$limit = isset($limit) ? $limit : 6;

$listTemplates = $IB -> getAllTemplates();

$templatesTop = array_slice($listTemplates,0,$limit);
$templatesBottom = array_slice($listTemplates,$limit);
?>

<ul class="templates-squares">
<?php
foreach ($templatesTop as $template){
    $template = $template['item'];
    ?>

    <li class="templates-square col-md-2 col-sm-4 col-xs-12">
        <form class="perform" method="post" action="/index.php?AJAX&TEMPLATE=cabinet_remittance_template&FinancialOperationType=<?=$template -> operationSubject -> type?>">
            <input type="hidden" name="templateId" value="<?=$template->id?>"/>
            <button type="submit" class="perform-btn" title="{CABINET-TEMPLATES-PREVIEW-TEMPLATE}">
                <div class="icon-place">
                    <?php if (!empty($template->iconRef) && file_exists($tpl -> pathFullTemplate."/img/content/templates/black-".$template->iconRef.".svg")){ ?>
                        <object class="icon-place__first js-template-icons" data-color="<?=(isset($template -> color) ? $template -> color : '')?>" type="image/svg+xml" data="<?=$tpl -> pathTemplate?>/img/content/templates/<?='black-'.$template->iconRef.'.svg'?>"></object>
                    <?php } ?>
                </div>
                <span><?=$template->title?></span>
            </button>
        </form>

        <span class="set">
            <a href="#" onclick="system_template_modal(jQuery(this)); return false;"
               data-template="<?= $template->id ?>"
               data-finance_type="<?= $template->operationSubject->type ?>"
            >
                <button title="{CABINET-TEMPLATES-EDIT-TEMPLATE}">
                    <i class="icons icons_settings-small"></i>
                </button>

            </a>
        </span>
    </li>

    <?php
}
?>
</ul>

<?php if($templatesBottom) { ?>
<div class="templates--wrap">
    <table class="templates-table">
        <tbody>
        <?php
        foreach ($templatesBottom as $template) {
            $template = $template['item'];
            $title = isset($template -> title) ? $template -> title : "";
            $amountInCents = isset($template->definedAmounts[0]->amount) ? $template->definedAmounts[0]->amount : 0;
            $currency = isset($template -> currency) ? $template -> currency : "";

            if (isset($IB->CONSTANTS['FinancialOperationType'][$template -> operationSubject -> type])):
                $icon = (!empty($template->iconRef) && file_exists($_SERVER['DOCUMENT_ROOT']."/dev_".TEMPLATE."/static/img/content/templates/black-".$template->iconRef."-small.svg")) ? $template->iconRef : "";
            ?>
                <tr class="template-table-row">
                    <?php
                    if($icon){
                        ?>
                        <td class="table-icon col-md-2 col-sm-2">
                            <object class="js-template-icons" data-color="<?=(isset($template -> color) ? $template -> color : '')?>" type="image/svg+xml" data="<?=$tpl -> pathTemplate?>/img/content/templates/black-<?=$template->iconRef.'-small.svg'?>"></object>
                        </td>
                        <?php
                    }
                    ?>
                    <td <?=($icon ? "" : "colspan='2'")?> class="name col-md-4 col-sm-4"><?=$title?></td>
                    <td class="money col-md-3 col-sm-3">
                        <span><?=$tpl->priceFormat($amountInCents)?></span>
                        <span class="money_currency"><?=$currency?></span>
                    </td>
                    <td class="actions-cat col-md-3 col-sm-3">
                        <form class="t-action-form" method="post" action="/index.php?AJAX&TEMPLATE=cabinet_remittance_template&FinancialOperationType=<?=$template -> operationSubject -> type?>">
                            <input type="hidden" name="templateId" value="<?=$template->id?>"/>
                            <button type="submit" title="{CABINET-TEMPLATES-EXECUTE-TEMPLATE}">
                                <a href="#">
                                    <i class="icons icons_round-arrows-temp"></i>
                                </a>
                            </button>
                        </form>


                        <a href="#" onclick="system_template_modal(jQuery(this)); return false;"
                           data-template="<?= $template->id ?>"
                           data-finance_type="<?= $template->operationSubject->type ?>"
                        >
                        <button type="submit" title="{CABINET-TEMPLATES-EDIT-TEMPLATE}">
                            <i class="icons icons_settings"></i>
                        </button>
                        </a>
                    </td>
                </tr>
            <?php
            endif;
        }
        ?>
        </tbody>
    </table>
</div>

<ul class="templates-table-mobile">
    <?php
    foreach ($templatesBottom as $template) {
    $template = $template['item'];
    $title = isset($template -> title) ? $template -> title : "";
    $lastExecutionAmount = isset($template -> lastExecutionAmount) ? $template -> lastExecutionAmount : "";
    $currency = isset($template -> currency) ? $template -> currency : "";
    if (isset($IB->CONSTANTS['FinancialOperationType'][$template -> operationSubject -> type])):
    ?>

    <li class="col-xs-12">
        <div class="col-xs-11">
            <span class="name"><?=$title?></span>
        </div>
        <div class="col-xs-11 money">
            <span><?=$tpl->priceFormat($lastExecutionAmount)?></span>
            <span class="money_currency"><?=$currency?></span>
            <a href="#" onclick="system_template_modal(jQuery(this)); return false;"
               data-template="<?= $template->id ?>"
               data-finance_type="<?= $template->operationSubject->type ?>"
            >
                <button type="submit" title="{CABINET-TEMPLATES-EDIT-TEMPLATE}">
                    <img src="<?=$tpl -> pathTemplate?>/img/content/cogtemplates.png">
                </button>
            </a>
        </div>
        <div class="col-xs-12 actions-cat">
            <form method="post" action="/index.php?AJAX&TEMPLATE=cabinet_remittance_template&FinancialOperationType=<?=$template -> operationSubject -> type?>">
                <input type="hidden" name="templateId" value="<?=$template->id?>"/>
                <button type="submit" title="{CABINET-TEMPLATES-EXECUTE-TEMPLATE}">
                    <a href="#">
                        <i class="icons icons_repeat"></i>
                        <span class="tooltip top" role="tooltip"></span>
                    </a>
                </button>
            </form>
        </div>
    </li>
    <?php
    endif;
    } ?>
</ul>

<?php } ?>