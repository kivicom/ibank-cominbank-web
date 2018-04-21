<?php
$currencyList = array('USD','EUR','RUB','UAH');

$rates = $IB -> request("getCurrencyExchangeRateList", [
    null,
    $currencyList,
    'bank',
    $IB -> SESS['TIME']*1000,
    new \Attributes()
]);

if ($IB->EXCEPTION) {
    $IB->EXCEPTION = '';
}


if ($rates) {
    ?>
    <script>
        var exchange = {
            <?php
            foreach ($rates as $index => $value) {?>
            <?=$value->currency?>: {
                buy: <?=round($value->buyRate, 2)?>,
                sell: <?=round($value->sellRate, 2)?>
            },
            <?php
            }
            ?>
        }
    </script>

    <div class="exchange-rate">
        <a name="exchange-js" style="height:0;visibility: hidden;width: 0;"></a>
        <div class="exchange-tab">
            <div class="buy-sell">
                <div class="buy-sell__name col-md-3 col-sm-12 col-xs-12">
                    <span>{CABINET-EXCHANGE-PURCHASE}:</span>
                </div>

                <ul class="buy-sell__rate col-md-9 col-sm-12 col-xs-12">
                    <li>
                        <span><?=$currencyList[0]?>&nbsp;</span>
                        <span><?=round($rates[0]->buyRate, 2)?></span>
                    </li>
                    <li>
                        <span><?=$currencyList[1]?>&nbsp;</span>
                        <span><?=round($rates[1]->buyRate, 2)?></span>
                    </li>
                    <li>
                        <span><?=$currencyList[2]?>&nbsp;</span>
                        <span><?=round($rates[2]->buyRate, 2)?></span>
                    </li>
                    <li class="buy-sell__helper"></li>
                </ul>
            </div>
            <div class="buy-sell buy-sell_last">
                <div class="buy-sell__name col-md-3 col-sm-12 col-xs-12">
                    <span>{CABINET-EXCHANGE-SALE}:</span>
                </div>

                <ul class="buy-sell__rate col-md-9 col-sm-12 col-xs-12">
                    <li>
                        <span><?=$currencyList[0]?>&nbsp;</span>
                        <span><?=round($rates[0]->sellRate, 2)?></span>
                    </li>
                    <li>
                        <span><?=$currencyList[1]?>&nbsp;</span>
                        <span><?=round($rates[1]->sellRate, 2)?></span>
                    </li>
                    <li>
                        <span><?=$currencyList[2]?>&nbsp;</span>
                        <span><?=round($rates[2]->sellRate, 2)?></span>
                    </li>
                    <li class="buy-sell__helper"></li>
                </ul>
            </div>
        </div>
    </div>

    <div id="exchange_modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{CABINET-EXCHANGE-CONVERTER}</h4>
                </div>
                <div class="modal-body">
                    <div class="exchange-rate">
                        <div class="exchange-tab">
                            <div class="buy-sell">
                                <div class="buy-sell__name col-md-3">
                                    <span>{CABINET-EXCHANGE-PURCHASE}:</span>
                                </div>

                                <ul class="buy-sell__rate col-md-9">
                                    <li>
                                        <span><?=$currencyList[0]?>&nbsp;</span>
                                        <span><?=round($rates[0]->buyRate, 2)?></span>
                                    </li>
                                    <li>
                                        <span><?=$currencyList[1]?>&nbsp;</span>
                                        <span><?=round($rates[1]->buyRate, 2)?></span>
                                    </li>
                                    <li>
                                        <span><?=$currencyList[2]?>&nbsp;</span>
                                        <span><?=round($rates[2]->buyRate, 2)?></span>
                                    </li>
                                    <li class="buy-sell__helper"></li>
                                </ul>
                            </div>
                            <div class="buy-sell buy-sell_last">
                                <div class="buy-sell__name col-md-3">
                                    <span>{CABINET-EXCHANGE-SALE}:</span>
                                </div>

                                <ul class="buy-sell__rate col-md-9">
                                    <li>
                                        <span><?=$currencyList[0]?>&nbsp;</span>
                                        <span><?=round($rates[0]->sellRate, 2)?></span>
                                    </li>
                                    <li>
                                        <span><?=$currencyList[1]?>&nbsp;</span>
                                        <span><?=round($rates[1]->sellRate, 2)?></span>
                                    </li>
                                    <li>
                                        <span><?=$currencyList[2]?>&nbsp;</span>
                                        <span><?=round($rates[2]->sellRate, 2)?></span>
                                    </li>
                                    <li class="buy-sell__helper"></li>
                                </ul>
                            </div>
                        </div>
                        <div class="exchange-tab">
                            <form class="buy-sell-calc exchange-rate-js">

                                <div class="calc-item col-md-12 col-sm-12 col-xs-12 calc-item__modal">
                                    <span class="absolute-name">{CABINET-EXCHANGE-SUMM}:</span>
                                    <input class="calc-input" data-mask="money">
                                </div>

                                <div class="calc-item">
                                    <span class="absolute-name">{CABINET-EXCHANGE-SALE}:</span>
                                    <div class="calc-select">
                                        <select class="exchange-from">
                                            <?php
                                            foreach ($currencyList as $index => $item) {
                                                ?>
                                                <option value="<?=$item?>" <?=(($index == 0) ? "selected='selected'" : "")?>><?=$item?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="calc-item replace-currencies-js">
                                    <span class="absolute-name">&nbsp;</span>
                                    <div class="calc-item__icons">
                                        <i class="icons icons_exchange-sized-grey"></i>
                                    </div>

                                </div>

                                <div class="calc-item">
                                    <span class="absolute-name">{CABINET-EXCHANGE-PURCHASE}:</span>
                                    <div class="calc-select">
                                        <select class="exchange-to">
                                            <?php
                                            foreach ($currencyList as $index => $item) {
                                                ?>
                                                <option value="<?=$item?>" <?=(($index == 1) ? "selected='selected'" : "")?>><?=$item?></option>
                                                <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="calc-item">
                                    <span class="absolute-name">&nbsp;</span>
                                    <div class="calc-item__icons">
                                        <i class="icons icons_equal-sized-grey"></i>
                                    </div>
                                </div>

                                <div class="calc-item">
                                    <span class="absolute-name">{CABINET-EXCHANGE-RESULT}:</span>
                                    <span class="calc-span">0.00</span>
                                </div>
                                <div class="calc-item__helper"></div>
                            </form>
                        </div>


                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function ($) {
            $(".exchange-rate-js").each(function () {
                var elem = $(this);

                elem.find(".calc-input").keyup(function () {
                    solveExchange(elem);
                });
                elem.find(".buy-sell-calc").change(function () {
                    solveExchange(elem);
                });
                solveExchange(elem);
            });


            function solveExchange(elem) {
                var val = elem.find(".calc-input").val();
                var from = elem.find(".exchange-from");
                var from_val = from.val().replace(/ /g,"");
                var to = elem.find(".exchange-to");
                var to_val = to.val().replace(/ /g,"");
                val = val.replace(/ /g,"");

                to.find("option").prop("disabled", false);
                to.find("option[value='"+from_val+"']").prop("disabled", true);

                if (from_val == to_val) {
                    to.find("option:not(:selected)").each(function () {
                        $(this).prop("selected", true);
                        return false;
                    })
                }

                var from_ = exchange[from_val].buy;
                var to_ = exchange[to_val].sell;

                var res = Math.ceil(100 * (from_ * val / to_)) / 100;

                elem.find(".calc-item .calc-span").html(res);
            }

            /*- Смена валют -*/

            $(".replace-currencies-js").click(function () {
                replaceCurrencies($(this).closest(".exchange-rate-js"));
            });

            function replaceCurrencies(elem) {
                var currencyList = <?=json_encode($currencyList)?>;

                var fromV = elem.find(".exchange-from").val();
                var toV = elem.find(".exchange-to").val();

                var fromI = currencyList.findIndex( function (i){
                    i == fromV;
                });
                var toI = currencyList.findIndex( function (i){
                    i == toV;
                });

                elem.find(".exchange-from").empty();
                elem.find(".exchange-to").empty();

                /*- индекс валюты и класс селекта -*/
                function addCurrenciesToSelect(curIndex,selectClass) {
                    for(i=0; i<currencyList.length;i++) {
                        if(i == curIndex){
                            $(selectClass).append('<option value="'+currencyList[i]+'"'+'selected="selected">'+currencyList[i]+'</option>');
                        }else{
                            $(selectClass).append('<option value='+currencyList[i]+'>'+currencyList[i]+'</option>');
                        }
                    }
                }
                addCurrenciesToSelect(toI,elem.find('.exchange-from'));
                addCurrenciesToSelect(fromI,elem.find('.exchange-to'));
                solveExchange(elem);
            }

        }(jQuery));
    </script>
    <?php
}
?>




