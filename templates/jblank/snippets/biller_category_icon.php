<?php
$iconsPath = "/images/icons/billers/";
$iconRoot = strtr($this->pathFullTemplate . $iconsPath, array("//" => "/"));
$filename = strtolower($item->id);
$filename = file_exists($iconRoot . $filename . ".svg") ? $tpl->pathTemplate . $iconsPath . $filename . ".svg" : $tpl->pathTemplate . $iconsPath. "default.png";