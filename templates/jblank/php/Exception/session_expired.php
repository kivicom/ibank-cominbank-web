<?php
$app = \JFactory::getApplication('site');
$session = \JFactory::getSession();
$session->set( 'IBModule', '');

header('Location: /cabinet/auth');
exit();




