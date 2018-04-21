<section class="content-sec clearfix">
    <?php

//    $result = $IB -> request("getAuthSession", [
//        $IB->TOKEN
//    ]);
//
//    print_r($result); die;
    ?>


<!--    <div class="container">-->
    <div class="content-sec__wrap">
        <div class="visible-lg">
            [cabinet_sidebar][/cabinet_sidebar]
        </div>

        <div class="content">
            <?php
            if ($_SERVER['REQUEST_URI'] != "/".$tpl->lang.$IB -> CONFIG -> get('CABINET_ENTRY_URL')) {
                ?>
                [breadcrumbs][/breadcrumbs]
                <?php
            }
            ?>

            <?=$content?>
        </div>
    </div>
</section>