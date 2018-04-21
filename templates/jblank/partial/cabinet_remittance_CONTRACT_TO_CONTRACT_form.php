<?php
$id_from = "select_" . uniqid();
$id_to = "select_" . uniqid();
$func = "func_" . uniqid();
?>
<script>
    var <?=$func?>_count = 0;

    var fromChange = false;

    jQuery("body").on("change", "#<?=$id_from?>", function(){
        fromChange = true;
        <?=$func?>(jQuery(this).closest("form"));
    });

    jQuery("body").on("change", "#<?=$id_to?>", function(){
        fromChange = false;
        <?=$func?>(jQuery(this).closest("form"));
    });

    function <?=$func?>(form) {
        (function ($) {
            <?=$func?>_count++;
            var fromId = $(".<?=$id_from?>_hidden[name='data[id]']").val();
            var fromType = $(".<?=$id_from?>_hidden[name='data[type]']").val();

            var toId = $(".<?=$id_to?>_hidden[name='data[id_to]']").val();
            var toType = $(".<?=$id_to?>_hidden[name='data[type_to]']").val();

            var DIRECT_TO = <?=(isset($_GET['data']) && isset($data['id_to'])) ? "true" : "false"?>;

            if (<?=$func?>_count < 3) {
                if (DIRECT_TO) {
                    $("#<?=$id_from?>").find("option[data-id='" + toId + "'][data-type='" + toType + "']").prop("disabled", true);
                    if ($("#<?=$id_from?>").find("option:selected").prop("disabled")) {
                        $("#<?=$id_from?>").find("option:enabled:first").prop("selected", true);
                    }
                } else {
                    $("#<?=$id_to?>").find("option[data-id='" + fromId + "'][data-type='" + fromType + "']").prop("disabled", true);
                    if ($("#<?=$id_to?>").find("option:selected").prop("disabled")) {
                        $("#<?=$id_to?>").find("option:enabled:first").prop("selected", true);
                    }
                }
            }

            if (<?=$func?>_count > 2) {
                if (fromChange) {
                    $("#<?=$id_to?>").find("option").prop("disabled", false);
                    $("#<?=$id_to?>").find("option[data-id='" + fromId + "'][data-type='" + fromType + "']").prop("disabled", true);
                    if ($("#<?=$id_to?>").find("option:selected").prop("disabled")) {
                        $("#<?=$id_to?>").find("option:enabled:first").prop("selected", true);
                    }
                } else {
                    $("#<?=$id_from?>").find("option").prop("disabled", false);
                    $("#<?=$id_from?>").find("option[data-id='" + toId + "'][data-type='" + toType + "']").prop("disabled", true);
                    if ($("#<?=$id_from?>").find("option:selected").prop("disabled")) {
                        $("#<?=$id_from?>").find("option:enabled:first").prop("selected", true);
                    }
                }
            }
        }(jQuery));
    }
</script>


<form class="cabinet_remittance_form_<?= $FinancialOperationTypeActive ?> validate"
      action="?FinancialOperationType=<?= $FinancialOperationTypeActive ?>" method="post">

    <input type="hidden" name="SERVICE" value="enroll" />

    <?php
    foreach ($data as $field => $parameter) {
        if ($parameter) echo '<input type="hidden" name="data[' . $field . ']" value="' . htmlspecialchars($parameter) . '" />';
    }

    $getParameters = $data;

    $disabled = false;

    include "cabinet_remittance_".$FinancialOperation."_fields.php";

    $IB->includes("blocks/remittance_form_submit", ['FinancialOperation' => $FinancialOperation]);
    ?>
</form>

<script>
    jQuery("#<?=$id_from?>").change();
    jQuery("#<?=$id_to?>").change();
</script>