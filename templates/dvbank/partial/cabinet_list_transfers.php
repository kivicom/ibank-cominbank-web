<li class="li-grid-item">
    <div class="overview overview_transfers  js-slide_toggle">
        <div class="overview__inner">
            <div class="services">
                <div class="services__name js-slide_toggle__event">
                    <i class="icons icons_chevron"></i>
                    <span>{CABINET-LIST-TRANSFERS-TITLE}</span>
                </div>
                <div class="btn-green">
                    <i class="icons icons_plus"></i>
                    <input class="btn-act" type="button" value="Відкрити">
                </div>

            </div>
            <div class="sub-services js-slide_toggle__block">
                <form action="" method="post">
                    <ul class="sub-services__list">
                        <li class="sub-services__item">
                            <div class="sub-services__acts">
                                <div class="acts__inner">
                                    <span>з картки:</span>
                                    <select>
                                        <option value="" selected>Оберіть картку</option>
                                        <option value="картка1">картка1</option>
                                        <option value="картка2">картка2</option>
                                        <option value="картка3">картка3</option>
                                        <option value="картка4">картка4</option>
                                        <option value="картка5">картка5</option>
                                    </select>
                                </div>
                                <div class="client-input">
                                    <div class="cards-num">
                                        <label for="cardNumber">Card number</label>
                                        <input name="data[cardNumber]" id="cardNumber" type="text" placeholder="XXXX-XXXX-XXXX-XXXX">
                                    </div>
                                    <div class="cards-amount">
                                        <label for="amount">Сума</label>
                                        <input name="data[cardAmount]" id="amount" type="text" placeholder="Сума">
                                    </div>
                                    <div class="purpose">
                                        <label for="purpose">Призначення платежу</label>
                                        <input name="data[purpose]" id="purpose" type="text" placeholder="Призначення платежу">
                                    </div>
                                </div>
                                <button class="btn-press">ПЕРЕКАЗАТИ</button>
                            </div>
                        </li>
                    </ul>
                </form>
            </div>
        </div>
    </div>
</li>