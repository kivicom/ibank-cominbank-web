<?php
$selected = isset($selected) ? $selected : [];
?>

<div <?=(isset($id) ? 'id="'.$id.'"' : '')?> class="input-group">
    <select name="data[expMonth]" class="required form-control" data-type="date" data-block="1"
            data-tag="month" data-condition="actual">
        <?php
        for ($i = 1; $i <= 12; $i++) {
            ?>
            <option value="<?= $i ?>" <?= (isset($selected['expMonth']) ? (($selected['expMonth'] == $i) ? "selected='selected'" : "") : "") ?>><?= $i ?></option>
            <?php
        }
        ?>
    </select>
    <span>/</span>
    <select name="data[expYear]" class="required form-control" data-type="date" data-block="1"
            data-tag="year" data-condition="actual">
        <?php
        $currentY = date("Y", time());
        for ($i = $currentY; $i <= ($currentY + 4); $i++) {
            ?>
            <option value="<?= $i ?>" <?= (isset($selected['expYear']) ? (($selected['expYear'] == $i) ? "selected='selected'" : "") : "") ?>><?= $i ?></option>
            <?php
        }
        ?>
    </select>
</div>