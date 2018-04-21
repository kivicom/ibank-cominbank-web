<form class="filters form-inline overview" method="get" action="">

    <?php
    foreach ($_GET as $field=>$value) {
        if (!is_array($value)) {
            ?>
            <input type="hidden" name="<?=$field?>" value="<?=$value?>" />
            <?php
        }
    }
    ?>

    <span class="filters__radio">
        <div class="form-group acts__inner acts__inner_blue">
            <label class="radio-inline">
                <input class="reload" type="radio" name="data[mode]" value="last" <?=($historyFinanceData['modeFilter'] == "last") ? "checked='checked'" : ""?>>
                <select class="reload form-control" name="data[last]">
                    <option value="10" <?=($historyFinanceData['lastFilter'] == "10") ? "selected='selected'" : ""?>>{CABINET-OPERATIONS-FILTER-10LASTDAYS}</option>
                    <option value="2"  <?=($historyFinanceData['lastFilter'] == "2") ? "selected='selected'" : ""?>>{CABINET-OPERATIONS-FILTER-2LASTDAYS}</option>
                </select>
            </label>
        </div>

        <div class="form-group">
            <label class="radio-inline">
                <input class="reload" type="radio" name="data[mode]" value="fromto" <?=($historyFinanceData['modeFilter'] == "fromto") ? "checked='checked'" : ""?>>
                {CABINET-CARD-MYOPERATIONS-FROM} <input class="datepicker form-control reload" name="data[from]" type="text" value="<?=$historyFinanceData['dateFrom']?>" />&nbsp;
                {CABINET-CARD-MYOPERATIONS-TO} <input class="datepicker form-control reload" name="data[to]" type="text"  value="<?=$historyFinanceData['dateTo']?>" />
            </label>
        </div>
    </span>
</form>