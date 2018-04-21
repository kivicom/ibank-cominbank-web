<?php
$props = [
    'title' => isset($props['title']) ? $props['title'] : '',
    'account' => isset($props['account']) ? $props['account'] : '',
    'date_from' => isset($props['date_from']) ? $props['date_from'] : '',
    'list' => isset($props['list']) ? $props['list'] : array(),
    'status' => isset($props['status']) ? $props['status'] : '',
    'balance' => isset($props['balance']) ? $props['balance'] : '',
    'operations' => isset($props['operations']) ? $props['operations'] : array(),
    'prev' => isset($props['prev']) ? $props['prev'] : '',
    'next' => isset($props['next']) ? $props['next'] : ''
];
?>


<div class="account-info">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6 col-sm-6 col-xs-12 account-info__left">
                    <div class="account-info__title">
                        <?=$props['prev'] ? '<a href="'.$props['prev'].'"><span class="account-info__prev glyphicon glyphicon-chevron-left"></span></a>' : ''?>
                        <?=$props['title']?><?=($props['account'] ? " <span class='account-info__account'>".$props['account']."</span>" : "")?>
                        <?=$props['next'] ? '<a href="'.$props['next'].'"><span class="account-info__next glyphicon glyphicon-chevron-right"></span></a>' : ''?>
                    </div>
                    <div class="account-info__from"><?=$props['date_from']?></div>
                    <ul class="account-info__list">
                        <li class="row">
                            <?php
                            foreach ($props['list'] as $field => $value) {
                                ?>
                                <div class="col-md-5 account-info__field"><?=$field?></div>
                                <div class="col-md-7 account-info__value"><?=$value?></div>
                                <?php
                            }
                            ?>
                        </li>
                    </ul>
                </div>

                <div class="col-md-6 col-sm-6 col-xs-12 account-info__right">
                    <div class="account-info__status"><?=$props['status']?></div>
                    <div class="account-info__balance"><?=$props['balance']?></div>

                    <div class="account-info__operations">
                        <?php
                        foreach ($props['operations'] as $value => $url) {
                            if (is_array($url)) {
                                $popover='<ul class=\'popover__list_default\'>';
                                foreach ($url as $value_ => $url_) {
                                    $popover .= "<li><a href='".$url_."'>".$value_."</a></li>";
                                }
                                $popover .='</ul>';
                                ?>
                                <button class="btn btn-def"  data-toggle="popover" data-placement="bottom" data-content="<?=$popover?>"><?=$value?></button>
                            <?php
                            } else {
                                ?>
                                <a href="<?=$url?>">
                                    <button class="btn btn-def"><?=$value?></button>
                                </a>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>