<?php
$link = isset($link) ? $link : $this -> SESS['REFER'];
?>


<div class="form-group row">
    <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
    <label class="col-md-3 col-sm-3 col-xs-12 text-left back">
        <img src="<?=$tpl -> pathTemplate?>/img/content/back-arrow.png">
        <a href="<?=$link?>">{CABINET-RETURN-BUTTON}</a>
    </label>
    <div class="col-md-7 col-sm-7 col-xs-12 text-right">

    </div>
    <div class="col-lg-1 col-md-1 col-sm-1 hidden-xs"></div>
</div>