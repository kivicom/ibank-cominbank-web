<div class="checkout-fields-wrap">
<?php

foreach ($checkoutFields as $key => $field){
    if($key == 'total') { ?>
        <div class="row">
            <hr/>
        </div>
        <?php } ?>
    <div class="row<?=($key == 'total')? ' totalamount' : '' ?>">
        <div class="checkout-fields__name col-md-6 col-sm-6 col-xs-6 text-left"><?=$field['label']?></div>
        <div class="checkout-fields__val col-md-6 col-sm-6 col-xs-6 text-right"><?=$field['value']?></div>
    </div>

    <?php
    if($key == 'to') { ?>
        <div class="row">
            <hr/>
        </div>
    <?php }
}
?>
</div>
