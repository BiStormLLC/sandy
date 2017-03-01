<?php
include '/var/www/slug/include/common.php';
$old_path = getcwd();
chdir('/vagrant/bistorm/iot/hdhomerun');
$output = shell_exec("./channel " . getChannel());
echo $output;
?>