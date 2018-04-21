<?php
$limit = isset($limit) ? $limit : 3;

$listTemplates = $IB -> getAllTemplates($limit);

if ($listTemplates) {
    ?>

    <li class="li-grid-item">
        <div class="overview overview_templates overview_templates--mob js-slide_toggle">
            <div class="overview__inner hidden-xxs">
                <div class="services">
                    <div class="services__name js-slide_toggle__event">
                        <i class="icons icons_chevron"></i>
                        <span>{CABINET-LIST-TEMPLATES-TITLE}</span>
                    </div>

                    <div class="btn-green">
                        <i class="icons icons_plus"></i>
                        <input class="btn-act" type="button" data-toggle="modal" data-target="#new_template_modal" value="{CABINET-TEMPLATES-NEW-BUTTON}">
                    </div>

                </div>
                <div class="products products_templates js-slide_toggle__block">

                    <table>
                        <tbody>
                        <?php
                        foreach ($listTemplates as $template) {
                            $template = $template['item'];

                            $description = $template->title ? $template->title : $template->description;

                            $amount = isset($template -> definedAmounts[0]) ? $template -> definedAmounts[0] -> amount : 0;

                            ?>
                            <tr class="products__item" onclick="jQuery(this).find('form').submit();">
                                <td class="name"><?= $tpl->string_cut($description, 35, '...', true) ?></td>
                                <td class="money">
                                    <span><?= $tpl->priceFormat($amount, false) ?></span><span
                                            class="money_currency"><?= $template->currency ?></span></td>
                                <td class="repeat">
                                    <form class="perform" method="post" action="/index.php?AJAX&TEMPLATE=cabinet_remittance_template&FinancialOperationType=<?=$template -> operationSubject -> type?>">
                                    <input type="hidden" name="templateId" value="<?= $template->id ?>"/>

                                        <input type="submit" value="{CABINET-TEMPLATES-EXECUTE-TEMPLATE}"/>
                                    </form>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        </tbody>
                    </table>

                </div>
            </div>

            <div class="overview__inner hidden-lg hidden-md hidden-sm hidden-xs col-xxs">
                <div class="services">
                    <div class="services__name">
                        <i class="icons icons_chevron"></i>
                        <span>Мої шаблони</span>
                    </div>


                </div>
                <div class="products products_templates">

                    <table>
                        <thead>
                        <tr>
                            <th class="table-name" colspan="3"><span>Банківські операції</span></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr class="products__item">
                            <td class="name">Оплата (комунал)</td>
                        </tr>
                        <tr class="products__item">
                            <td class="money"><span>500&nbsp000&nbsp000&nbsp000</span><span
                                        class="money_currency">uah</span></td>
                        </tr>
                        <tr class="products__item">
                            <td class="repeat"><a href="#"><i class="icons"></i>Повторити</a></td>
                        </tr>
                        <tr class="products__item">
                            <td class="name">Длинное название с перескакивающим текстом</td>
                        </tr>
                        <tr class="products__item">
                            <td class="money"><span>500&nbsp000&nbsp000&nbsp000</span><span
                                        class="money_currency">uah</span></td>
                        </tr>
                        <tr class="products__item">
                            <td class="repeat"><a href="#"><i class="icons"></i>Повторити</a></td>
                        </tr>
                        <tr class="products__item">
                            <td class="name">Длинное название с текстом</td>
                        </tr>
                        <tr class="products__item">
                            <td class="money"><span>500&nbsp000&nbsp000&nbsp000</span><span
                                        class="money_currency">uah</span></td>
                        </tr>
                        <tr class="products__item">
                            <td class="repeat"><a href="#"><i class="icons"></i>Повторити</a></td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
        <div id="new_template_modal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">{CABINET-TEMPLATES-NEW}</h4>
                    </div>
                    <div class="modal-body">
                        <ul>
                            <?php
                            foreach ($IB -> CONSTANTS['FinancialOperationType'] as $id => $name) {
                                if ($id <> 8) continue;
                                ?>
                                <li><a href="#" data-dismiss="modal" data-toggle="modal" data-target="#new_template_prep_modal"
                                       onclick="
                                               jQuery('#new_template_prep_modal').find('.modal-title').html('{CABINET-OPETATION-<?=$name?>}');
                                               jQuery('.new_template_prep_input').attr('href', '/<?=$tpl->lang.$IB -> CONFIG -> get('CABINET_REMITTANCE_URL')."?FinancialOperationType=".$id?>');
                                               ">{CABINET-OPETATION-<?=$name?>}</a></li>
                                <?php
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div id="new_template_prep_modal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"></h4>
                    </div>
                    <div class="modal-body">
                        {CABINET-TEMPLATES-TEMPLATE-CREATE}
                    </div>
                    <div class="modal-footer">
                        <a class="new_template_prep_input" href=""><button type="button" class="btn btn-primary">{CABINET-REMITTANCE-PAYMENT-INPUT}</button></a>
                        <button type="button" class="btn btn-default" data-dismiss="modal">{BUTTON-CANCEL}</button>
                    </div>
                </div>
            </div>
        </div>


    </li>
    <?php
}
?>