<?php
$result = $IB -> request("logout");

$app = \JFactory::getApplication('site');
$session = \JFactory::getSession();
$session->set( 'IBModule', '');

header('Location: /cabinet/auth');
exit();
