<?php
$title = isset($title) ? $title : "";
?>


<ul class="biller__nav">
    <li class="active">{CABINET-REMITTANCE-STEP} 1</li>
    <li class="<?=(($SERVICE == "commit") or ($SERVICE == "success")) ? "active" : ""?>" onclick="return false;">{CABINET-REMITTANCE-STEP} 2</li>
    <li class="<?=($SERVICE == "success") ? "active" : ""?>">{CABINET-REMITTANCE-STEP} 3</li>
    <li><?=$title?></li>
</ul>