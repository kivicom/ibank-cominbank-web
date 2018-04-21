<?php
$id = isset($id) ? $id : false;
$id = $id ? $id : ("select" . uniqid());

$getAllAccounts = $IB->getAllAccounts($ContractType);
$func = "select" . uniqid() . "_";
$selected = isset($selected) ? $selected : [];
$paremeters = (isset($paremeters) && is_array($paremeters)) ? $paremeters : ['id', 'type', 'cardId' => 'card', 'currency'];
$disabled = isset($disabled) ? $disabled : false;

foreach ($paremeters as $field => $paremeter) {
    if (is_numeric($field)) {
        $paremeters[$paremeter] = $paremeter;
        unset($paremeters[$field]);
    }
}
?>

<select <?= $id ? ("id='" . $id . "'") : "" ?> class="form-control"
                                               onchange="<?= $func ?>()" <?= $disabled ? 'disabled="disabled"' : "" ?>>
    <?php
    $compared = [];
    foreach ($getAllAccounts as $index => $value) {
        $coincided = 0;
        foreach ($selected as $field => $val)
            $coincided += (false !== ($paramId = array_search($field, $paremeters)) && (isset($value[$paramId])) && ($value[$paramId] == $val)) ?  1 : 0;
        $compared[$index] = $coincided;
    }
    arsort($compared, true);
    $compared = array_slice($compared, 0, 1, true);
    $checked = (key($compared) !== null) ? key($compared) : 0;

    foreach ($getAllAccounts as $index => $value) {
        ?>
        <option value="<?= $value['id'] ?>"
                data-id="<?= $value['id'] ?>"
                data-type="<?= $value['type'] ?>"
                data-currency="<?= $value['currency'] ?>"
                data-balance="<?= $value['balance'] ?>"
                data-balance_formated="<?= htmlentities($tpl->priceFormat($value['balance']) . " " . $value['currency']) ?>"
                data-cardid="<?= (isset($value['cardId']) ? $value['cardId'] : "") ?>"
                data-title="<?= $value['title'] ?>"
            <?php
            if ($index == $checked) echo 'selected="selected"';
            ?>
        >
            <?= ($value['title'] . " (" . ($tpl->priceFormat($value['balance']) . " " . $value['currency']) . ")") ?>
        </option>
        <?php
    }
    ?>
</select>

<script>
    function <?=$func?>() {
        (function ($) {
            var elem = $("#<?=$id?>");
            var params = <?=json_encode($paremeters)?>;
            var selected = $("#<?=$id?> option:selected");
            var datas = selected.data();
            var form = elem.closest("form");
            form.find(".<?=$id?>_hidden").remove();

            for (var field in datas) {
                for (var param in params) {
                    if (((field == params[param].toLowerCase()) && $.isNumeric(param)) || ((field == param.toLowerCase()) && !$.isNumeric(param))) {
                        form.append('<input class="<?=$id?>_hidden" type="hidden" name="data[' + params[param] + ']" value="' + datas[field] + '" />');
                        break;
                    }
                }
            }
            form.change();
        }(jQuery));
    }

    <?=$func?>();
</script>