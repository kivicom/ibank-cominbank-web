<?php
/**
 * branches.php
 * XML file of all branches
 */

$rss_content = <<<XML

XML;

$rss_content = <<<XML
<locations>$rss_content</locations>
XML;
header( "Content-type: application/xml; charset=utf-8" );
echo $rss_content;


