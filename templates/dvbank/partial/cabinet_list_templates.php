<?php
if($IB->userRole('full')){
$limit = isset($limit) ? $limit : 6;

$listTemplates = $IB -> getAllTemplates($limit);

    ?>

    <li class="li-grid-item">
        <div class="overview overview_templates overview_templates--mob">
            <div class="overview__inner overview__inner_block">
                <div class="services">
                    <div class="services__name">
                        <span>{CABINET-LIST-TEMPLATES-TITLE}</span>
                    </div>

                </div>
                <div class="products products_templates custom__scroll--js">
<!--                    <ul class="sub-services--title sub-services--title_templates clearfix">-->
<!--                        <li class="sub-services--item col-lg-6 col-md-6 col-sm-6 col-xs-6 tabs--js active"><a href="#" class="sub-services__link">{CABINET-LIST-TEMPLATES-GENERAL}</a></li>-->
<!--                        <li class="sub-services--item col-lg-6 col-md-6 col-sm-6 col-xs-6 tabs--js"><a href="#" class="sub-services__link">{CABINET-LIST-TEMPLATES-FAVOURITES}</a></li>-->
<!--                    </ul>-->
                    <?php if ($listTemplates) { ?>
                    <ul class="sub-services--inner tabs__list clearfix">
                        <li class="list--item col-lg-12 col-md-12 col-sm-12 col-xs-12 active">
                            <table>
<!--                                <thead>-->
<!--                                <tr class="categories categories--operations">-->
<!--                                    <th>{CABINET-OPETATION-TABLE-SUBJECT}</th>-->
<!--                                    <th class="money">{CABINET-OPETATION-TABLE-AMOUNT}</th>-->
<!--                                    <th class="actions-cat">{CABINET-OPETATION-TABLE-OPERATIONS}</th>-->
<!--                                </tr>-->
<!--                                </thead>-->
                                <tbody>
                                <?php
                                foreach ($listTemplates as $template) {
                                    $template = $template['item'];

                                    $description = $template->title ? $template->title : $template->description;

                                    $amount = isset($template -> definedAmounts[0]) ? $template -> definedAmounts[0] -> amount : 0;

                                    ?>

                                    <tr class="products__item" onclick="jQuery(this).find('form').submit();">
                                        <td class="name" align="left"><?= $tpl->string_cut($description, 35, '...', true) ?></td>
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
                        </li>
                        <li class="list--item col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <table>
                                <tbody>
                                <?php
                                foreach ($listTemplates as $template) {
                                    $template = $template['item'];

                                    $description = $template->title ? $template->title : $template->description;

                                    $amount = isset($template -> definedAmounts[0]) ? $template -> definedAmounts[0] -> amount : 0;

                                    ?>

                                    <tr class="products__item" onclick="jQuery(this).find('form').submit();">
                                        <td class="name" align="left"><?= $tpl->string_cut($description, 35, '...', true) ?></td>
                                        <td class="money">
                                            <span><?= $tpl->priceFormat($amount, false) ?></span><span
                                                    class="money_currency"><?= $template->currency ?></span></td>
                                        <td class="repeat">
                                            <form method="post"
                                                  action="/<?= ($tpl->lang . $IB -> CONFIG -> get('CABINET_REMITTANCE_URL')) ?>/?FinancialOperationType=<?= $template->operationSubject->type ?>&template">
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
                        </li>
                    </ul>

                    <?php } else { ?>
                        <div class="if-empty__templates">
                            <span>{CABINET-LIST-TEMPLATES-EMPTY}</span>
                        </div>
                        <?php
                    }
                    if ($listTemplates) { ?>
                        <a href="<?=($tpl->lang.$IB -> CONFIG -> get('CABINET_TEMPLATES_URL'))?>" class="sub-services__link sub-services__link_other sub-services__link_all">{CABINET-TEMPLATES-ALLTEMPLATES}</a>
                    <?php } ?>
                    <a data-toggle="modal" data-target="#new_template_modal" class="sub-services__link sub-services__link_other sub-services__link_new">{CABINET-TEMPLATES-NEW-BUTTON}</a>

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
                            $templatesOperationTypes = $IB -> CONSTANTS['FinancialOperationType'];
                            unset($templatesOperationTypes[9]);
                            unset($templatesOperationTypes[10]);
                            foreach ($templatesOperationTypes as $id => $name) {
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
                        <a class="new_template_prep_input" href=""><button type="button" class="btn-green btn-green_pay">{CABINET-REMITTANCE-PAYMENT-INPUT}</button></a>
                        <button type="button" class="btn btn-default" data-dismiss="modal">{BUTTON-CANCEL}</button>
                    </div>
                </div>
            </div>
        </div>


    </li>
<?php } ?>