<?php
include 'slug.php';

$trail = new \BiStorm\SLUG\Slug('bistorm', array(), true);

$trail->exec();
exit();

?>